<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function serve(Request $request)
    {
        Log::info('FrontendController::serve called for path: ' . $request->path());

        // For API requests, we should handle them in Laravel, not block them
        if (str_starts_with($request->path(), 'api/')) {
            Log::info('Routing to API: ' . $request->path());
            // Let Laravel handle API routes normally
            // This will allow the API routes to be processed by Laravel's router
            return app()->handle($request);
        }

        // For admin requests (Filament), let Laravel handle them normally
        if (str_starts_with($request->path(), 'admin/') || $request->path() === 'admin') {
            Log::info('Routing to Admin: ' . $request->path());
            // Let Laravel handle admin routes normally
            return app()->handle($request);
        }

        // For Filament system routes
        if (str_starts_with($request->path(), 'filament/')) {
            Log::info('Routing to Filament: ' . $request->path());
            // Let Laravel handle Filament system routes normally
            return app()->handle($request);
        }

        // For broadcasting routes
        if (str_starts_with($request->path(), 'broadcasting/')) {
            Log::info('Routing to Broadcasting: ' . $request->path());
            // Let Laravel handle broadcasting routes normally
            return app()->handle($request);
        }

        // SPECIAL FIX: Redirect Next.js Hot Module Replacement (HMR) directly to Port 3000
        // This prevents the "Loading..." loop caused by PHP trying to proxy an infinite stream
        if (str_contains($request->path(), 'webpack-hmr')) {
            return redirect('http://127.0.0.1:3000/' . $request->path(), 307);
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
                        "connect-src 'self' http://127.0.0.1:8000 ws://127.0.0.1:3000 http://127.0.0.1:3000",
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

        // FIX HMR: Inject script to redirect WebSocket connections to port 3000
        if (stripos($response, '</head>') !== false) {
            $hmrFixScript = <<<'SCRIPT'
<script>
(function() {
    // Override WebSocket to redirect HMR connections to port 3000
    const OriginalWebSocket = window.WebSocket;
    window.WebSocket = function(url, protocols) {
        // If this is an HMR connection, redirect to port 3000
        if (url.includes('webpack-hmr') || url.includes('_next')) {
            url = url.replace('ws://127.0.0.1:8000', 'ws://127.0.0.1:3000');
            url = url.replace('ws://localhost:8000', 'ws://localhost:3000');
        }
        return new OriginalWebSocket(url, protocols);
    };
    // Copy static properties
    window.WebSocket.CONNECTING = OriginalWebSocket.CONNECTING;
    window.WebSocket.OPEN = OriginalWebSocket.OPEN;
    window.WebSocket.CLOSING = OriginalWebSocket.CLOSING;
    window.WebSocket.CLOSED = OriginalWebSocket.CLOSED;
})();
</script>
SCRIPT;
            $response = str_replace('</head>', $hmrFixScript . '</head>', $response);
            $laravelResponse->setContent($response);
        }

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
