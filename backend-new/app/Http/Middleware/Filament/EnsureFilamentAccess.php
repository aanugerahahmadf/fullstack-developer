<?php

namespace App\Http\Middleware\Filament;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureFilamentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow Filament admin routes to proceed normally
        if (str_starts_with($request->path(), 'admin')) {
            // Skip authentication check for login routes to prevent redirect loops
            if (str_contains($request->path(), 'login')) {
                return $next($request);
            }

            // Check if user is authenticated and has the super-admin role
            if (Auth::check() && Auth::user()->hasRole('super-admin')) {
                return $next($request);
            }

            // If not the super admin, redirect to login
            return redirect()->route('filament.admin.auth.login');
        }

        // Allow API routes to proceed normally
        if (str_starts_with($request->path(), 'api')) {
            return $next($request);
        }

        // Allow Laravel internal routes to proceed normally
        if (in_array($request->path(), ['up'])) {
            return $next($request);
        }

        // For all other requests, continue with the next middleware
        return $next($request);
    }
}
