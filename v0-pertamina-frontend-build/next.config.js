/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  reactStrictMode: false,
  // Disable experimental features that may cause timeout issues
  experimental: {
    // optimizeCss: true, // Disabled to prevent timeout
    // optimizeServerReact: true, // Disabled to prevent timeout
  },
  serverExternalPackages: [],
  // Add empty turbopack config to silence error (we'll use --webpack flag instead)
  turbopack: {},
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
  // Add webpack configuration to handle HMR issues
  webpack: (config, { isServer }) => {
    // Ensure lucide-react can be resolved properly
    config.resolve.fallback = {
      ...config.resolve.fallback,
    };
    
    // Don't alias lucide-react - let webpack resolve it normally
    // The transpilePackages config should handle it
    
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
  // HTTP streaming for faster response (no buffering)
  httpAgentOptions: {
    keepAlive: true,
    keepAliveMsecs: 1000,
    maxSockets: 50,
    maxFreeSockets: 10,
  },
  // Disable output buffering for instant responses
  onDemandEntries: {
    maxInactiveAge: 25 * 1000,
    pagesBufferLength: 2,
  },
  // Optimize font loading
  // optimizeFonts: true,  // Removed as it's not recognized
};

module.exports = nextConfig;