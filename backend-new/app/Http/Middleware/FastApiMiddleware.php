<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class FastApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Apply only to API routes
        if (!$this->isApiRoute($request)) {
            return $next($request);
        }

        // For GET requests, check cache first before processing
        if ($request->isMethod('GET')) {
            $cacheKey = 'api_response_' . md5($request->fullUrl());
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse && is_array($cachedResponse)) {
                // Check if cached response is still valid (1 second TTL for maximum performance)
                if (time() - ($cachedResponse['timestamp'] ?? 0) < 1) {
                    return response($cachedResponse['content'], $cachedResponse['status'])
                        ->withHeaders($cachedResponse['headers'] ?? []);
                }
            }
        }

        // Get response
        $response = $next($request);

        // Apply optimizations only to successful GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Apply aggressive caching for API responses
            $this->applyCaching($request, $response);

            // Optimize headers for performance
            $this->optimizeHeaders($response);
        }

        return $response;
    }

    private function isApiRoute(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/');
    }

    private function applyCaching(Request $request, Response $response): void
    {
        // Generate cache key based on request
        $cacheKey = 'api_response_' . md5($request->fullUrl());

        // Cache the response for 3 seconds (ultra-aggressive caching)
        if ($request->isMethod('GET')) {
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
                'timestamp' => time()
            ];

            Cache::put($cacheKey, $cacheData, 1); // 1 second cache for maximum performance
        }
    }

    private function optimizeHeaders(Response $response): void
    {
        // Remove unnecessary headers that add overhead
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Set performance headers
        $response->headers->set('Cache-Control', 'public, max-age=1, stale-while-revalidate=10');
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 1) . ' GMT');

        // Add compression header
        $response->headers->set('Vary', 'Accept-Encoding');

        // Set connection to keep-alive for better performance
        $response->headers->set('Connection', 'keep-alive');
    }
}
