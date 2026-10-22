<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingAdminController extends Controller
{
    /**
     * List all bookings across the platform.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.screen.cinema', 'seats'])
            ->orderByDesc('id')
            ->get();

        return $this->successResponse(
            BookingResource::collection($bookings),
            'All bookings retrieved successfully.'
        );
    }
}

