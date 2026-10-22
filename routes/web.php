<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Movie Ticket Booking API',
        'version' => '1.0.0',
        'status' => 'operational',
        'docs' => '/api/v1',
    ]);
});

// Render / Uptime Health Check endpoint
Route::get('/api/health', [HealthController::class, 'check']);

