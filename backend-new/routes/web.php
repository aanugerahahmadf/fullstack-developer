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
    $frontendPath = base_path('v0-pertamina-frontend-build/public/images/' . $any);
    if (File::exists($frontendPath)) {
        return response()->file($frontendPath);
    }

    abort(404);
})->where('any', '.*');

// Serve the Next.js frontend application
// Handle all HTTP methods for the frontend (GET, POST, PUT, PATCH, DELETE, etc.)
// But exclude API, admin, and images routes
Route::any('/{any?}', [FrontendController::class, 'serve'])
    ->where('any', '^(?!api\/|admin\/|images\/).*$')
    ->name('frontend.serve');
