<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    /**
     * Health check endpoint for Render and uptime monitoring.
     */
    public function check(): JsonResponse
    {
        $dbStatus = 'ok';
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            $dbStatus = 'disconnected';
            $dbError = $e->getMessage();
        }

        $isHealthy = $dbStatus === 'ok';

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'api' => 'up',
                'database' => $dbStatus,
            ],
            'database_error' => config('app.debug') ? $dbError : null,
        ], $isHealthy ? 200 : 503);
    }
}

