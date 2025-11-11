import express from 'express';
import { createProxyMiddleware } from 'http-proxy-middleware';
import path from 'path';
import { fileURLToPath } from 'url';

// Create Express app with performance optimizations
const app = express();

// Disable unnecessary middleware for maximum speed
app.disable('x-powered-by');
app.set('etag', false);

// Increase payload limit
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ limit: '10mb', extended: true }));

// Serve static files from the frontend build
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
app.use(express.static(path.join(__dirname, '../v0-pertamina-frontend-build/.next/static')));
app.use('/images', express.static(path.join(__dirname, '../v0-pertamina-frontend-build/public/images')));

// Proxy API requests to the Laravel backend (now running on port 8000)
app.use('/api', createProxyMiddleware({
  target: 'http://127.0.0.1:8000',
  changeOrigin: true,
  pathRewrite: {
    '^/api': '/api'
  }
}));

// Proxy admin panel requests to the Laravel backend (now running on port 8000)
app.use('/admin', createProxyMiddleware({
  target: 'http://127.0.0.1:8000',
  changeOrigin: true
}));

// Serve the Next.js frontend (running on port 3000)
app.use('/', createProxyMiddleware({
  target: 'http://localhost:3000',
  changeOrigin: true,
  ws: true
}));

// Start the server with optimized settings on port 8000
const port = 8000;
const hostname = '127.0.0.1';

// Check if port is available
const server = app.listen(port, hostname, (err) => {
  if (err) {
    if (err.code === 'EADDRINUSE') {
      console.log(`Port ${port} is already in use. Trying to kill existing process...`);
      process.exit(1);
    } else {
      throw err;
    }
  }
  console.log(`> Ready on http://${hostname}:${port}`);
  console.log(`> Access the application at http://${hostname}:${port}`);
});

// Ultra-fast server settings
server.keepAliveTimeout = 10000;
server.headersTimeout = 10000;
server.requestTimeout = 10000;

// Enable connection pooling
server.on('connection', (socket) => {
  socket.setNoDelay(true);
  socket.setKeepAlive(true, 1000);
});
