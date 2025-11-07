import path from 'path';
import { exec } from 'child_process';
import express from 'express';
import { createProxyMiddleware } from 'http-proxy-middleware';
import http from 'http';
import { Agent as HttpAgent } from 'http';
import cluster from 'cluster';
import os from 'os';
import { fileURLToPath } from 'url';
import { dirname } from 'path';
import compression from 'compression';

// Get __dirname equivalent in ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Simple in-memory micro-cache for API GET responses
const apiCache = new Map();
const API_CACHE_TTL_MS = parseInt(process.env.API_CACHE_TTL_MS || '5000', 10);
const API_CACHE_MAX_SIZE = parseInt(process.env.API_CACHE_MAX_SIZE || '200', 10);
const API_CACHE_MAX_BODY = parseInt(process.env.API_CACHE_MAX_BODY || '1048576', 10); // 1MB

function pruneCache() {
  if (apiCache.size <= API_CACHE_MAX_SIZE) return;
  const now = Date.now();
  for (const [key, entry] of apiCache) {
    if (entry.expiresAt <= now || apiCache.size > API_CACHE_MAX_SIZE) {
      apiCache.delete(key);
    }
    if (apiCache.size <= API_CACHE_MAX_SIZE) break;
  }
}

const port = process.env.PORT || 8000;
const workers = parseInt(process.env.WORKERS || os.cpus().length, 10);

function createServer() {
  // Create Express app
  const app = express();

// Optimize Express settings for performance
app.set('trust proxy', 1);
app.disable('x-powered-by');

// Enable compression for faster responses
app.use(compression({
  level: 6,
  threshold: 1024,
}));

// Serve static files from Laravel's public directory with strong caching
app.use(express.static(path.join(__dirname, 'public'), {
  maxAge: '30d',
  etag: false,
  lastModified: false,
  setHeaders: (res, filePath) => {
    // Mark hashed assets as immutable for long-term caching
    if (/[.-][0-9a-f]{8,}\./i.test(filePath)) {
      res.setHeader('Cache-Control', 'public, max-age=31536000, immutable');
    } else {
      res.setHeader('Cache-Control', 'public, max-age=2592000');
    }
  }
}));

// API micro-cache middleware (served before proxy)
function apiCacheMiddleware(req, res, next) {
  if (req.method !== 'GET') return next();
  if (req.headers['x-bypass-cache']) return next();
  const key = `GET:${req.originalUrl}`;
  const now = Date.now();
  const cached = apiCache.get(key);
  if (cached && cached.expiresAt > now) {
    for (const [hKey, hVal] of Object.entries(cached.headers)) {
      if (typeof hVal === 'string') res.setHeader(hKey, hVal);
    }
    res.status(cached.statusCode);
    return res.send(cached.body);
  }
  return next();
}

// Keep-alive agent for upstreams
const upstreamAgent = new HttpAgent({ keepAlive: true, maxSockets: 256, maxFreeSockets: 32, keepAliveMsecs: 60000 });

// Proxy API requests to Laravel with optimized settings
app.use('/api', createProxyMiddleware({
  target: 'http://127.0.0.1:8000', // Use port 8000 for Laravel
  changeOrigin: true,
  agent: upstreamAgent,
  // Optimize proxy settings for speed
  proxyTimeout: 15000,
  timeout: 15000,
  // Enable keep-alive connections and add cache hints for GET requests
  onProxyReq: (proxyReq, req, res) => {
    proxyReq.setHeader('Connection', 'keep-alive');
    // Remove Accept-Encoding header to prevent double compression
    proxyReq.removeHeader('Accept-Encoding');
  },
  onProxyRes: (proxyRes, req, res) => {
    if (req.method === 'GET') {
      proxyRes.headers['cache-control'] = proxyRes.headers['cache-control'] || 'public, max-age=5, stale-while-revalidate=55';
    }
    // Buffer small JSON/text responses for micro-cache
    const contentType = String(proxyRes.headers['content-type'] || '');
    const cacheable = req.method === 'GET' && (contentType.includes('application/json') || contentType.includes('text/'));
    if (!cacheable) return;
    let size = 0;
    const chunks = [];
    proxyRes.on('data', (chunk) => {
      size += chunk.length;
      if (size <= API_CACHE_MAX_BODY) chunks.push(chunk);
    });
    proxyRes.on('end', () => {
      if (size > API_CACHE_MAX_BODY) return;
      const bodyBuffer = Buffer.concat(chunks);
      const key = `GET:${req.originalUrl}`;
      const headersToStore = {};
      for (const [hKey, hVal] of Object.entries(proxyRes.headers)) {
        if (typeof hVal === 'string') headersToStore[hKey] = hVal;
      }
      apiCache.set(key, {
        statusCode: proxyRes.statusCode || 200,
        headers: headersToStore,
        body: bodyBuffer,
        expiresAt: Date.now() + API_CACHE_TTL_MS,
      });
      pruneCache();
    });
  }
}));

// Proxy admin requests to Laravel
app.use('/admin', createProxyMiddleware({
  target: 'http://127.0.0.1:8000',
  changeOrigin: true,
  agent: upstreamAgent,
  proxyTimeout: 15000,
  timeout: 15000,
  // Enable keep-alive connections
  onProxyReq: (proxyReq, req, res) => {
    proxyReq.setHeader('Connection', 'keep-alive');
    // Remove Accept-Encoding header to prevent double compression
    proxyReq.removeHeader('Accept-Encoding');
  }
}));

// Serve Next.js frontend for all other routes
app.use('/', createProxyMiddleware({
  target: 'http://127.0.0.1:3002',
  changeOrigin: true,
  agent: upstreamAgent,
  proxyTimeout: 15000,
  timeout: 15000,
  onProxyRes: (proxyRes, req, res) => {
    // Aggressive caching for Next.js static assets
    if (req.url.startsWith('/_next/static/')) {
      proxyRes.headers['cache-control'] = 'public, max-age=31536000, immutable';
    } else if (req.url.startsWith('/_next/image')) {
      proxyRes.headers['cache-control'] = 'public, max-age=604800';
    }
  },
  onError: (err, req, res) => {
    console.error('Proxy error:', err);
    // If proxy fails, send a more helpful message
    if (req.url.startsWith('/api')) {
      res.status(500).send('Backend API not available');
    } else {
      res.status(500).send('Frontend not available');
    }
  }
}));

  // Start server with tuned timeouts for better keep-alive performance
  const server = http.createServer(app);
  server.keepAliveTimeout = 65000;
  server.headersTimeout = 66000;
  server.requestTimeout = 60000;

  server.listen(port, () => {
    console.log(`Worker ${process.pid} running at http://localhost:${port}`);
  });
}

if (cluster.isPrimary && workers > 1) {
  for (let i = 0; i < workers; i += 1) cluster.fork();
  cluster.on('exit', () => cluster.fork());
  console.log(`Master ${process.pid} started ${workers} workers`);
} else {
  createServer();
}
