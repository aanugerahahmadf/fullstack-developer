<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function serve(Request $request)
    {
        // For API requests, we should handle them in Laravel, not block them
        if (str_starts_with($request->path(), 'api/')) {
            // Let Laravel handle API routes normally
            // This will allow the API routes to be processed by Laravel's router
            return app()->handle($request);
        }

        // For admin requests (Filament), don't serve frontend
        if (str_starts_with($request->path(), 'admin')) {
            abort(404);
        }

        // Proxy the request to the Next.js frontend running on port 3000
        $url = "http://127.0.0.1:3000/" . ltrim($request->path(), '/');

        // Create context for the request with maximum performance settings
        $contextOptions = [
            'http' => [
                'method' => $request->method(),
                'header' => $this->formatHeaders($request->headers->all()),
                'timeout' => 30, // Reduced timeout for better responsiveness
                'ignore_errors' => true,
                'protocol_version' => 1.1,
                'follow_location' => 1,
                'max_redirects' => 3,
            ]
        ];

        // Add request body for POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $contextOptions['http']['content'] = $request->getContent();
        }

        $context = stream_context_create($contextOptions);

        // Make the request with error handling
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            // Log the error for debugging
            Log::error('Frontend service unavailable', [
                'url' => $url,
                'method' => $request->method(),
                'path' => $request->path()
            ]);

            // Return a minimal error response for maximum speed
            return response('Frontend service unavailable', 503);
        }

        // Get the response headers
        $headers = $http_response_header ?? [];
        $statusCode = 200;

        // Parse status code and headers
        if (!empty($headers)) {
            // First header contains HTTP status
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $headers[0], $matches)) {
                $statusCode = (int)$matches[1];
            }

            // Process other headers
            $skipHeaders = ['transfer-encoding', 'connection', 'keep-alive', 'server', 'date'];
            $responseHeaders = [];

            for ($i = 1; $i < count($headers); $i++) {
                $header = $headers[$i];
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Skip certain headers that can cause issues
                    if (!in_array(strtolower($key), $skipHeaders)) {
                        $responseHeaders[$key] = $value;
                    }
                }
            }
        }

        // Create Laravel response with the content and headers
        $laravelResponse = response($response, $statusCode);

        // Forward response headers
        if (isset($responseHeaders)) {
            foreach ($responseHeaders as $key => $value) {
                // Special handling for content-security-policy
                if (strtolower($key) === 'content-security-policy') {
                    // Modify CSP to allow connections to the API
                    $value = str_replace(
                        "connect-src 'self'",
                        "connect-src 'self' http://127.0.0.1:8000",
                        $value
                    );
                }
                // Skip content-length header to prevent buffering issues
                if (strtolower($key) !== 'content-length') {
                    $laravelResponse->header($key, $value);
                }
            }
        }

        // Add performance headers
        $laravelResponse->header('X-Proxy-Latency', '0');
        $laravelResponse->header('X-Content-Type-Options', 'nosniff');

        return $laravelResponse;
    }

    /**
     * Format headers for HTTP context
     */
    private function formatHeaders(array $headers): string
    {
        $formatted = [];
        foreach ($headers as $key => $values) {
            foreach ($values as $value) {
                // Skip certain headers that can cause issues
                if (!in_array(strtolower($key), ['host', 'connection', 'transfer-encoding', 'expect'])) {
                    $formatted[] = "$key: $value";
                }
            }
        }
        return implode("\r\n", $formatted);
    }
}
