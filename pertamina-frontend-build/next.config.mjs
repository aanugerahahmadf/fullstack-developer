import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  outputFileTracingRoot: __dirname,
  reactStrictMode: false,
  // Configure asset prefix for Laravel proxy compatibility
  assetPrefix: process.env.NODE_ENV === 'production' ? undefined : '',
  // Use webpack for better compatibility
  experimental: {
    optimizeCss: true,
    // Enable faster page loading
    optimizeServerReact: true,
  },
  serverExternalPackages: [],
  // Add transpilePackages to handle lucide-react properly
  transpilePackages: ['lucide-react'],
  // Configure image optimization
  images: {
    unoptimized: true,
    // Allow images from the local domain using remotePatterns instead of domains
    remotePatterns: [
      {
        protocol: 'http',
        hostname: '127.0.0.1',
        port: '8000',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8000',
      },
    ],
  },
  // Add webpack configuration if needed
  webpack: (config, { isServer }) => {
    // Webpack config can be extended here if needed
    return config;
  },
  // Add performance optimizations
  poweredByHeader: false,
  compress: true,
  // Configure dev server for better performance
  devIndicators: {
    buildActivity: false,
  },
  // Additional performance optimizations for fastest response times
  compiler: {
    removeConsole: {
      exclude: ['error', 'warn'],
    },
  },
  // Performance optimizations for standalone mode
  // swcMinify: true,  // Removed as it's not recognized
  // Cache configuration for better performance
  cacheMaxMemorySize: 0,
  // HTTP streaming for faster response
  httpAgentOptions: {
    keepAlive: true,
  },
  // Optimize font loading
  // optimizeFonts: true,  // Removed as it's not recognized
};

export default nextConfig;