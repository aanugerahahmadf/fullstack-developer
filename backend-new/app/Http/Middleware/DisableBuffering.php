<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableBuffering
{
    /**
     * Handle an incoming request.
     * Disables all output buffering for instant response delivery
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Disable PHP output buffering completely
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Disable output buffering at PHP level
        ini_set('output_buffering', '0');
        ini_set('zlib.output_compression', '0');
        ini_set('implicit_flush', '1');
        
        // Get the response
        $response = $next($request);
        
        // Add headers to prevent buffering at all levels
        $response->headers->set('X-Accel-Buffering', 'no'); // Nginx
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Transfer-Encoding', 'chunked'); // Enable chunked transfer for streaming
        
        // Remove Content-Length to allow streaming (no buffering)
        $response->headers->remove('Content-Length');
        
        return $response;
    }
}

