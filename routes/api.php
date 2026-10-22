<?php

use App\Http\Controllers\Api\V1\Admin\BookingAdminController;
use App\Http\Controllers\Api\V1\Admin\CinemaAdminController;
use App\Http\Controllers\Api\V1\Admin\MovieAdminController;
use App\Http\Controllers\Api\V1\Admin\ScreenAdminController;
use App\Http\Controllers\Api\V1\Admin\ShowtimeAdminController;
use App\Http\Controllers\Api\V1\Admin\StatsAdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MovieController;
use App\Http\Controllers\Api\V1\ShowtimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Prefix: /api/v1 configured in bootstrap/app.php)
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/health', [HealthController::class, 'check']);

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::match(['put', 'patch'], '/profile', [AuthController::class, 'updateProfile']);
        Route::match(['put', 'patch'], '/password', [AuthController::class, 'updatePassword']);
    });
});

// Public Catalog Routes
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{movie}', [MovieController::class, 'show']);
Route::get('/movies/{movie}/showtimes', [MovieController::class, 'showtimes']);
Route::get('/showtimes/{id}/seats', [ShowtimeController::class, 'seats']);

// Protected User Booking Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']); // frontend compatibility
});

// Admin Management Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [StatsAdminController::class, 'stats']);
    Route::get('/bookings', [BookingAdminController::class, 'index']);

    Route::apiResource('movies', MovieAdminController::class);
    Route::apiResource('cinemas', CinemaAdminController::class);
    Route::apiResource('screens', ScreenAdminController::class);
    Route::apiResource('showtimes', ShowtimeAdminController::class);
});

