<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('DebugMiddleware called', [
            'path' => $request->path(),
            'url' => $request->url(),
            'method' => $request->method(),
        ]);

        $response = $next($request);

        Log::info('DebugMiddleware response', [
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
