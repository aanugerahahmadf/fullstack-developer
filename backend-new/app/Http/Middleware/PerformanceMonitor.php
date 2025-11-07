<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    public function handle(Request $request, Closure $next)
    {
        // Only monitor API routes
        if (!$this->isApiRoute($request)) {
            return $next($request);
        }

        // Record start time
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate execution time
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        // Log slow requests (over 100ms)
        if ($executionTime > 100) {
            Log::warning('Slow API Request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => round($executionTime, 2),
                'status_code' => $response->getStatusCode(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Add performance header for debugging
        $response->headers->set('X-Response-Time', round($executionTime, 2) . 'ms');

        return $response;
    }

    private function isApiRoute(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/');
    }
}
