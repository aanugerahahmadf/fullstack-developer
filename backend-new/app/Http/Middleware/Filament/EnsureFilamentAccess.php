<?php

namespace App\Http\Middleware\Filament;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EnsureFilamentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log that this middleware is being called
        Log::info('EnsureFilamentAccess middleware called', [
            'path' => $request->path(),
            'url' => $request->url(),
        ]);

        // Allow Filament admin routes to proceed normally
        if (str_starts_with($request->path(), 'admin')) {
            // Skip authentication check for login routes to prevent redirect loops
            if (str_contains($request->path(), 'login') || str_contains($request->path(), 'logout')) {
                return $next($request);
            }

            // Check if user is authenticated and has the Super Admin role
            if (Auth::check()) {
                $user = Auth::user();
                // Check if user has Super Admin role using Spatie Permission
                // Using a database query approach to avoid IDE intelephense error
                $superAdminRole = Role::where('name', 'Super Admin')->first();
                if ($superAdminRole && $user->roles->contains($superAdminRole)) {
                    return $next($request);
                }
            }

            // If not the super admin, redirect to login
            Log::info('Redirecting to login - user not super admin');
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
        Log::info('Continuing with next middleware');
        return $next($request);
    }
}