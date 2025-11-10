<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For maximum performance, only monitor in debug mode
        if (config('app.debug')) {
            $start = microtime(true);
            $response = $next($request);
            $time = microtime(true) - $start;

            // Log only slow requests (>100ms) to reduce overhead
            if ($time > 0.1) {
                Log::info('Slow API Request', [
                    'url' => $request->url(),
                    'time' => $time,
                    'method' => $request->method()
                ]);
            }

            // Add performance headers only in debug mode
            $response->headers->set('X-Response-Time', $time * 1000);
            return $response;
        }

        // In production, skip monitoring for maximum speed
        return $next($request);
    }
}
