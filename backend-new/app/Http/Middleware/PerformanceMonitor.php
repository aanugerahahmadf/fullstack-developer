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
        // For maximum performance in production, skip monitoring entirely
        if (!config('app.debug')) {
            return $next($request);
        }
        
        // In debug mode, monitor performance with minimal overhead
        $start = microtime(true);
        $response = $next($request);
        $time = microtime(true) - $start;

        // Log only very slow requests (>100ms) to minimize overhead
        if ($time > 0.1) {
            Log::debug('API Request Performance', [
                'url' => $request->url(),
                'time_ms' => round($time * 1000, 2),
                'method' => $request->method(),
                'memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ]);
        }

        return $response;
    }
}
