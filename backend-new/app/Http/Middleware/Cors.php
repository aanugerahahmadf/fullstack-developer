<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight requests with optimized settings for speed
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        // Allow multiple origins for development with optimized lookup
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:3002',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:3001',
            'http://127.0.0.1:3002',
            'http://127.0.0.1:8000'
        ];

        $origin = $request->header('Origin');
        
        // Optimized CORS header setting for maximum speed
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            // Fallback to wildcard for development
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }

        // Set CORS headers for optimal performance
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        // Reduced max-age for development but still reasonable
        $response->headers->set('Access-Control-Max-Age', '3600'); 
        // Remove Vary header for better caching in development
        // $response->headers->set('Vary', 'Origin');

        // Essential exposed headers only
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}
