<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FrontendBridgeController extends \App\Http\Controllers\Controller
{
    /**
     * Serve the Next.js frontend through Laravel
     */
    public function serveFrontend(Request $request)
    {
        // Get the requested path
        $path = $request->path();

        // Remove 'frontend' prefix if present
        $path = str_replace('frontend/', '', $path);

        // Construct the URL to the Next.js server (now on port 3000)
        $url = "http://127.0.0.1:3000/" . ltrim($path, '/');

        try {
            // Forward the request to Next.js
            $response = Http::withOptions([
                'timeout' => 30,
                'connect_timeout' => 10,
            ])->withHeaders([
                'Accept' => $request->header('Accept', '*/*'),
                'User-Agent' => $request->header('User-Agent', 'Laravel Frontend Bridge'),
            ])->send($request->method(), $url, [
                'body' => $request->getContent(),
                'headers' => $request->headers->all(),
            ]);

            // Return the response from Next.js
            return response($response->body(), $response->status())
                ->withHeaders($response->headers());

        } catch (\Exception $e) {
            Log::error('Frontend bridge error', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a fallback response
            return response('Frontend service temporarily unavailable', 503);
        }
    }

    /**
     * Get frontend asset
     */
    public function serveAsset(Request $request, string $path)
    {
        $url = "http://127.0.0.1:3000/" . $path;

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->withHeaders([
                        'Content-Type' => $response->header('Content-Type', 'application/octet-stream'),
                        'Cache-Control' => 'public, max-age=31536000',
                    ]);
            }

            return response('Asset not found', 404);
        } catch (\Exception $e) {
            Log::error('Frontend asset error', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return response('Asset service unavailable', 503);
        }
    }
}
