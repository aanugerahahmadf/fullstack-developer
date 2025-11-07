<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $end = microtime(true);
        $duration = ($end - $start) * 1000; // Convert to milliseconds

        // Log slow requests (over 100ms)
        if ($duration > 100) {
            Log::warning('Slow API request', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'duration_ms' => $duration
            ]);
        }

        // Add response time header
        $response->headers->set('X-Response-Time', $duration . 'ms');

        return $response;
    }
}
