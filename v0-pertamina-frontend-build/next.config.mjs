/** @type {import('next').NextConfig} */
const nextConfig = {
  typescript: {
    ignoreBuildErrors: true,
  },
  // Enable Next.js image optimization and modern formats
  images: {
    formats: ['image/avif', 'image/webp'],
    // allow default optimizer; do not set unoptimized: true
  },
  compress: true,
  swcMinify: true,
  poweredByHeader: false,
  // Use standalone output for server deployment
  output: 'standalone',
  // Avoid extra redirects from trailing slash
  trailingSlash: false,
  // Configure allowed development origins
  allowedDevOrigins: ['http://127.0.0.1:8000', 'http://localhost:8000'],
  async headers() {
    return [
      {
        source: '/_next/static/:path*',
        headers: [
          { key: 'Cache-Control', value: 'public, max-age=31536000, immutable' },
        ],
      },
      {
        source: '/_next/image',
        headers: [
          { key: 'Cache-Control', value: 'public, max-age=604800' },
        ],
      },
    ]
  },
}

export default nextConfig