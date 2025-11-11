<?php

// Test Filament route registration
use Illuminate\Support\Facades\Route;

require_once 'public/index.php';

// Get all routes
echo "=== REGISTERED ROUTES ===\n";
$routes = Route::getRoutes();
$count = 0;
foreach ($routes as $route) {
    if (strpos($route->uri, 'admin') !== false) {
        echo "✓ " . implode('|', $route->methods) . " " . $route->uri . " => " . $route->getName() . "\n";
        $count++;
        if ($count > 20) break;
    }
}
echo "Total admin routes: $count\n";
