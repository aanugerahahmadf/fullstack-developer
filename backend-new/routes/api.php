<?php

use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CctvController;
use App\Http\Controllers\Api\FrontendBridgeController;
use App\Http\Controllers\Api\ProductionTrendController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\UnitPerformanceController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\PerformanceMonitor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "api"
| middleware group. Enjoy building your API!
|
*/

// Public API routes with performance monitoring
Route::middleware([PerformanceMonitor::class, Cors::class])->group(function () {
    // Fast API endpoints with aggressive caching
    Route::get('/stats', [StatsController::class, 'index'])->name('api.stats.index');
    Route::get('/contact', [ContactController::class, 'index'])->name('api.contact.index');

    // Production Trends API endpoint
    Route::get('/production-trends', [ProductionTrendController::class, 'index'])->name('api.production-trends.index');
    
    // Unit Performance API endpoint
    Route::get('/unit-performance', [UnitPerformanceController::class, 'index'])->name('api.unit-performance.index');
    Route::get('/unit-performance/{unitName}', [UnitPerformanceController::class, 'show'])->name('api.unit-performance.show');

    // Stateful API endpoints
    Route::get('/buildings', [BuildingController::class, 'index'])->name('api.buildings.index');
    Route::get('/buildings/{id}', [BuildingController::class, 'show'])->name('api.buildings.show');

    // Room API endpoints
    Route::get('/rooms', [RoomController::class, 'index'])->name('api.rooms.index');
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('api.rooms.show');
    Route::get('/rooms/building/{buildingId}', [RoomController::class, 'getByBuilding'])->name('api.rooms.by-building');

    // CCTV API endpoints
    Route::get('/cctvs', [CctvController::class, 'index'])->name('api.cctvs.index');
    Route::get('/cctvs/{id}', [CctvController::class, 'show'])->name('api.cctvs.show');
    Route::get('/cctvs/room/{roomId}', [CctvController::class, 'getByRoom'])->name('api.cctvs.by-room');
    Route::get('/cctvs/stream/{id}', [CctvController::class, 'getStreamUrl'])->name('api.cctvs.stream-url');

    // Frontend bridge routes
    Route::get('/frontend/{any?}', [FrontendBridgeController::class, 'serveFrontend'])
        ->where('any', '.*')
        ->name('api.frontend.serve');

    // Frontend assets
    Route::get('/frontend-assets/{path}', [FrontendBridgeController::class, 'serveAsset'])
        ->where('path', '.*')
        ->name('api.frontend.asset');
});
