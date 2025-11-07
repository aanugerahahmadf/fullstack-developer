<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (strtoupper($request->method()) === 'GET') {
            // Conservative micro-caching at edge: 1s + SWR 10s (align with proxy)
            $response->headers->set('Cache-Control', 'public, max-age=1, stale-while-revalidate=10');

            // Weak ETag based on content length + last-modified guess
            $etagBase = ($response->headers->get('Content-Length') ?: '') . '|' . substr(sha1((string) $response->getContent()), 0, 12);
            $etag = 'W/"' . $etagBase . '"';
            $response->headers->set('ETag', $etag);

            if ($request->headers->get('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent(null);
            }
        }

        // Ensure no X-Powered-By leaks
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}


