<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\V1\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * List all bookings for the currently authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = $request->user()->bookings()
            ->with(['showtime.movie', 'showtime.screen.cinema', 'seats'])
            ->orderByDesc('id')
            ->get();

        return $this->successResponse(
            BookingResource::collection($bookings),
            'Bookings retrieved successfully.'
        );
    }

    /**
     * Create a new ticket booking.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $booking = $this->bookingService->createBooking(
            user: $user,
            showtimeId: (int) $validated['showtime_id'],
            seatIdentifiers: $validated['seats'],
            status: $request->input('status', 'confirmed')
        );

        return $this->successResponse(
            new BookingResource($booking),
            'Booking successfully created.',
            201
        );
    }

    /**
     * Show a specific booking.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $booking = Booking::with(['showtime.movie', 'showtime.screen.cinema', 'seats'])
            ->findOrFail($id);

        Gate::authorize('view', $booking);

        return $this->successResponse(
            new BookingResource($booking),
            'Booking details retrieved.'
        );
    }

    /**
     * Cancel an existing booking.
     */
    public function cancel(int $id, Request $request): JsonResponse
    {
        $booking = Booking::with(['showtime'])->findOrFail($id);

        Gate::authorize('cancel', $booking);

        $cancelledBooking = $this->bookingService->cancelBooking($booking);

        return $this->successResponse(
            new BookingResource($cancelledBooking),
            'Booking cancelled successfully.'
        );
    }
}

