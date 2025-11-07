/** @type {import('next').NextConfig} */
const nextConfig = {
  typescript: {
    ignoreBuildErrors: true,
  },
  images: {
    unoptimized: true,
  },
  // Use standalone output for server deployment
  output: 'standalone',
  // Ensure proper routing
  trailingSlash: true,
  // Configure allowed development origins
  allowedDevOrigins: ['http://127.0.0.1:8000', 'http://localhost:8000'],
}

export default nextConfig