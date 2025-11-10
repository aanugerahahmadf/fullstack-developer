/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  reactStrictMode: false,
  // Use Turbopack for better performance
  experimental: {
    optimizeCss: true,
    // Enable faster page loading
    optimizeServerReact: true,
  },
  serverExternalPackages: [],
  // Enhanced turbopack config for better HMR stability
  turbopack: {
    resolveExtensions: ['.ts', '.tsx', '.js', '.jsx', '.json', '.mjs'],
  },
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
    // Handle HMR issues with lucide-react
    if (!isServer) {
      config.resolve.alias = {
        ...config.resolve.alias,
        'lucide-react': 'lucide-react/dist/esm/index.js',
      };
    }
    
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