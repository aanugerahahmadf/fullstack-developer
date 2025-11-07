<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ProxyToFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the incoming request
        Log::info('ProxyToFrontend: Processing request for ' . $request->path());

        // Don't proxy API requests (check if path starts with api/)
        if (str_starts_with($request->path(), 'api/')) {
            Log::info('ProxyToFrontend: Skipping API request');
            return $next($request);
        }

        // Don't proxy Filament admin requests (including login)
        if (str_starts_with($request->path(), 'admin')) {
            Log::info('ProxyToFrontend: Skipping admin request');
            return $next($request);
        }

        // Don't proxy Laravel internal routes
        if (in_array($request->path(), ['up'])) {
            Log::info('ProxyToFrontend: Skipping internal route');
            return $next($request);
        }

        // Proxy all other requests to the Next.js standalone server
        return $this->proxyToNextJs($request);
    }

    /**
     * Proxy request to Next.js server
     */
    private function proxyToNextJs(Request $request)
    {
        try {
            // Proxy to the standalone Next.js server running on port 3001
            $url = "http://127.0.0.1:3001/" . ltrim($request->path(), '/');
            Log::info('ProxyToFrontend: Proxying to ' . $url);

            // Use Laravel's HTTP client to make the request with the same method
            $method = strtolower($request->method());
            $response = Http::$method($url, $request->all());

            // Get the content and headers
            $content = $response->body();
            $headers = $response->headers();

            // Remove problematic headers
            $cleanHeaders = [];
            foreach ($headers as $key => $value) {
                if (!in_array(strtolower($key), ['transfer-encoding'])) {
                    // Handle array values
                    if (is_array($value)) {
                        $cleanHeaders[$key] = implode(', ', $value);
                    } else {
                        $cleanHeaders[$key] = $value;
                    }
                }
            }

            // Add CORS headers
            $cleanHeaders['Access-Control-Allow-Origin'] = '*';
            $cleanHeaders['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS';
            $cleanHeaders['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-Requested-With';

            Log::info('ProxyToFrontend: Successfully proxied request, status: ' . $response->status());
            return response($content, $response->status())->withHeaders($cleanHeaders);
        } catch (\Exception $e) {
            Log::error('ProxyToFrontend error: ' . $e->getMessage());
            // Return a simple response if proxy fails
            return response('Frontend not available', 404)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }
    }
}
