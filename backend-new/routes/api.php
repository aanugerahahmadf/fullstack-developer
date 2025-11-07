<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\CctvController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\ChartController;

// Stats
Route::get('/stats', [StatsController::class, 'index']);

// Chart Data
Route::get('/chart/production-trends', [ChartController::class, 'productionTrends']);
Route::get('/chart/unit-performance', [ChartController::class, 'unitPerformance']);

// Buildings
Route::get('/buildings', [BuildingController::class, 'index']);
Route::get('/buildings/{id}', [BuildingController::class, 'show']);

// Rooms
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::get('/rooms/building/{buildingId}', [RoomController::class, 'getByBuilding']);

// CCTVs
Route::get('/cctvs', [CctvController::class, 'index']);
Route::get('/cctvs/{id}', [CctvController::class, 'show']);
Route::get('/cctvs/room/{roomId}', [CctvController::class, 'getByRoom']);
Route::get('/cctvs/{id}/stream-url', [CctvController::class, 'getStreamUrl']);

// Contacts
Route::get('/contacts', [ContactController::class, 'index']);

// No authentication routes needed for this application
