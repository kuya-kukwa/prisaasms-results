import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Enable React Strict Mode
  reactStrictMode: true,

  // Performance optimizations
  poweredByHeader: false,
  compress: true,

  // Turbopack configuration (updated from experimental.turbo)
  turbopack: {
    rules: {
      '*.svg': {
        loaders: ['@svgr/webpack'],
        as: '*.js',
      },
    },
  },

  // Experimental features for better performance
  experimental: {
    // Performance optimizations (conditional for development speed)
    optimizeCss: process.env.NODE_ENV === 'production',
    webpackBuildWorker: process.env.NODE_ENV === 'production',

    // Better tree shaking
    optimizePackageImports: [
      '@heroicons/react',
      '@radix-ui/react-dialog',
      '@radix-ui/react-dropdown-menu',
      'lucide-react',
      'framer-motion'
    ],
  },

  // Image optimization for faster loading
  images: {
    // Enable modern formats
    formats: ['image/webp', 'image/avif'],

    // Optimize for development
    deviceSizes: [640, 750, 828, 1080, 1200, 1920, 2048, 3840],
    imageSizes: [16, 32, 48, 64, 96, 128, 256, 384],

    // Faster image loading
    minimumCacheTTL: 60,
    dangerouslyAllowSVG: true,
    contentDispositionType: 'attachment',
    contentSecurityPolicy: "default-src 'self'; script-src 'none'; sandbox;",

    // Allow external image domains if needed
    remotePatterns: [
      {
        protocol: 'https',
        hostname: '**',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
      },
    ],
  },

  // Webpack optimizations
  webpack: (config, { dev, isServer }) => {
    // Optimize bundle splitting
    if (!dev && !isServer) {
      config.optimization.splitChunks.chunks = 'all';
      config.optimization.splitChunks.cacheGroups = {
        ...config.optimization.splitChunks.cacheGroups,
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          chunks: 'all',
          priority: 10,
        },
        radix: {
          test: /[\\/]node_modules[\\/]@radix-ui[\\/]/,
          name: 'radix-ui',
          chunks: 'all',
          priority: 20,
        },
      };
    }

    // Add performance hints
    if (!dev) {
      config.performance = {
        hints: 'warning',
        maxEntrypointSize: 512000,
        maxAssetSize: 512000,
      };
    }

    return config;
  },

  // Production optimizations
  ...(process.env.NODE_ENV === 'production' && {
    swcMinify: process.env.NODE_ENV === 'production',
    // Optimize output
    output: 'standalone',
  })
};

export default nextConfig;