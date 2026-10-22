<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SeatResource;
use App\Models\BookingSeat;
use App\Models\Showtime;
use Illuminate\Http\JsonResponse;

class ShowtimeController extends Controller
{
    /**
     * Get seat map for a given showtime with available/taken status.
     */
    public function seats(int $id): JsonResponse
    {
        /** @var Showtime $showtime */
        $showtime = Showtime::with(['screen.seats'])->findOrFail($id);

        $screen = $showtime->screen;

        // Ensure seats exist for the screen
        if ($screen->seats->isEmpty()) {
            $screen->generateSeats();
            $screen->load('seats');
        }

        // Get all booked seat IDs for this showtime
        $takenSeatIds = BookingSeat::where('showtime_id', $showtime->id)
            ->pluck('seat_id')
            ->flip()
            ->all();

        // Sort seats cleanly by row label and seat number
        $sortedSeats = $screen->seats->sortBy([
            ['row_label', 'asc'],
            ['seat_number', 'asc'],
        ]);

        $seatResources = $sortedSeats->map(function ($seat) use ($takenSeatIds) {
            $isTaken = isset($takenSeatIds[$seat->id]);
            return new SeatResource($seat, $isTaken);
        })->values();

        return $this->successResponse(
            $seatResources,
            'Seat map retrieved successfully.'
        );
    }
}

