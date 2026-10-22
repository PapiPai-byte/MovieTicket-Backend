<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;

class StatsAdminController extends Controller
{
    public function __construct(
        protected StatsService $statsService
    ) {}

    /**
     * Get platform statistics for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->statsService->getDashboardStats();

        return $this->successResponse(
            $stats,
            'Dashboard stats retrieved successfully.'
        );
    }
}

