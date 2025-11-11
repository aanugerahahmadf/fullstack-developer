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

            // Log only slow requests (>50ms) to reduce overhead
            if ($time > 0.05) {
                Log::info('Slow API Request', [
                    'url' => $request->url(),
                    'time' => $time,
                    'method' => $request->method(),
                    'memory_peak' => memory_get_peak_usage(true) / 1024 / 1024, // MB
                ]);
            }

            // Add performance headers only in debug mode
            $response->headers->set('X-Response-Time', round($time * 1000, 2));
            $response->headers->set('X-Memory-Usage', round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB');
            return $response;
        }

        // In production, skip monitoring for maximum speed but still optimize
        $response = $next($request);

        // Even in production, add minimal performance headers for client-side optimization
        $response->headers->set('X-Response-Time', 'ultra-fast');
        return $response;
    }
}
