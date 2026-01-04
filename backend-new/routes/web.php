<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\FrontendController;

// Serve static assets directly from the frontend build
Route::get('/images/{any}', function ($any) {
    $path = storage_path('app/public/images/' . $any);
    if (File::exists($path)) {
        return response()->file($path);
    }

    // Also check in the frontend public directory
    $frontendPath = base_path('pertamina-frontend-build/public/images/' . $any);
    if (File::exists($frontendPath)) {
        return response()->file($frontendPath);
    }

    abort(404);
})->where('any', '.*');

// IMPORTANT: Filament admin routes are registered automatically by the AdminPanelProvider
// The /admin routes will be handled by Filament before reaching this fallback

// All API routes are handled separately in routes/api.php

// Fallback: Serve the Next.js frontend application only for remaining routes
// This will ONLY match routes that don't match any explicit routes above
// Does NOT include: /admin/*, /api/*, /filament/*, etc (they're handled before this)
Route::fallback(function (FrontendController $controller) {
    return $controller->serve(request());
});
