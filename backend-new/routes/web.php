<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontendController;
use App\Http\Middleware\ProxyToFrontend;

// Serve the Next.js frontend application
// Handle all HTTP methods for the frontend (GET, POST, PUT, PATCH, DELETE, etc.)
// Apply ProxyToFrontend middleware selectively to avoid conflicts with Filament
Route::match(['get', 'post', 'put', 'patch', 'delete'], '/{any?}', [FrontendController::class, 'serve'])
    ->where('any', '.*')
    ->middleware(ProxyToFrontend::class);
