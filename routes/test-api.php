<?php

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\SystemLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Test API Routes (No Authentication)
|--------------------------------------------------------------------------
|
| These routes are for testing purposes only and should be removed
| in production. They bypass all authentication middleware.
|
*/

// Test route for API status
Route::get('/status', function () {
    return response()->json([
        'status' => 'Test API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Device API Routes (no auth for testing)
Route::prefix('devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::get('/statistics', [DeviceController::class, 'statistics']);
    Route::post('/sync', [DeviceController::class, 'sync']);
    Route::get('/{id}', [DeviceController::class, 'show']);
    Route::get('/{id}/modal-data', [DeviceController::class, 'modalData']);
    Route::get('/{id}/logs', [DeviceController::class, 'logs']);
    Route::post('/{id}/control', [DeviceController::class, 'control']);
});

// System Log API Routes (no auth for testing)
Route::prefix('system-logs')->group(function () {
    Route::get('/', [SystemLogController::class, 'index']);
    Route::get('/statistics', [SystemLogController::class, 'statistics']);
    Route::get('/health', [SystemLogController::class, 'health']);
    Route::post('/sync', [SystemLogController::class, 'sync']);
    Route::post('/cleanup', [SystemLogController::class, 'cleanup']);
    Route::get('/{id}', [SystemLogController::class, 'show']);
});

// Dashboard API Routes (no auth for testing)
Route::prefix('dashboard')->group(function () {
    Route::get('/devices', [DeviceController::class, 'dashboardDevices']);
    Route::get('/device-stats', [DeviceController::class, 'dashboardStats']);
    Route::get('/hierarchy', [DeviceController::class, 'hierarchy']);
    Route::get('/haos-stats', [SystemLogController::class, 'haosStats']);
});