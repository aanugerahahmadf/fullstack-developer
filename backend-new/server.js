const path = require('path')
const { exec } = require('child_process')
const express = require('express')
const { createProxyMiddleware } = require('http-proxy-middleware')

// Create Express app
const app = express()
const port = process.env.PORT || 8000

// Optimize Express settings for performance
app.set('trust proxy', 1)
app.disable('x-powered-by')

// Enable compression for faster responses
app.use(require('compression')())

// Serve static files from Laravel's public directory with aggressive caching
app.use(express.static(path.join(__dirname, 'public'), {
  maxAge: '1d',
  etag: false,
  lastModified: false
}))

// Proxy API requests to Laravel with optimized settings
app.use('/api', createProxyMiddleware({
  target: 'http://127.0.0.1:8001', // Use port 8001 for Laravel
  changeOrigin: true,
  pathRewrite: {
    '^/api': '/api'
  },
  // Optimize proxy settings for speed
  proxyTimeout: 3000,
  timeout: 3000,
  // Enable keep-alive connections
  onProxyReq: (proxyReq, req, res) => {
    proxyReq.setHeader('Connection', 'keep-alive')
  }
}))

// Proxy admin requests to Laravel
app.use('/admin', createProxyMiddleware({
  target: 'http://127.0.0.1:8001',
  changeOrigin: true,
  proxyTimeout: 3000,
  timeout: 3000,
  // Enable keep-alive connections
  onProxyReq: (proxyReq, req, res) => {
    proxyReq.setHeader('Connection', 'keep-alive')
  }
}))

// Serve Next.js frontend for all other routes
app.use('/', createProxyMiddleware({
  target: 'http://127.0.0.1:3001',
  changeOrigin: true,
  proxyTimeout: 3000,
  timeout: 3000,
  onError: (err, req, res) => {
    console.error('Proxy error:', err)
    res.status(500).send('Frontend proxy error')
  }
}))

// Start server
app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`)
})
