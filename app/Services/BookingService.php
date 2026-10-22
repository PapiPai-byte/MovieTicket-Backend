<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\SeatType;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Create a new booking within a DB transaction with row locking.
     *
     * @param  User  $user
     * @param  int  $showtimeId
     * @param  array<int|string>  $seatIdentifiers  Seat IDs or codes (e.g. "A1", "B2")
     * @param  string  $status  'confirmed' or 'pending'
     * @return Booking
     *
     * @throws ValidationException
     */
    public function createBooking(User $user, int $showtimeId, array $seatIdentifiers, string $status = 'confirmed'): Booking
    {
        // 1. Validate max seats
        if (count($seatIdentifiers) < 1 || count($seatIdentifiers) > 8) {
            throw ValidationException::withMessages([
                'seats' => ['You must select between 1 and 8 seats per booking.'],
            ]);
        }

        // 2. Run inside DB transaction with row locking
        return DB::transaction(function () use ($user, $showtimeId, $seatIdentifiers, $status) {
            // Lock the showtime row for update
            /** @var Showtime $showtime */
            $showtime = Showtime::where('id', $showtimeId)
                ->with(['screen', 'movie'])
                ->lockForUpdate()
                ->firstOrFail();

            // Business rule: Cannot book past showtimes
            if ($showtime->isPast()) {
                throw ValidationException::withMessages([
                    'showtime_id' => ['Cannot book tickets for a showtime that has already started or passed.'],
                ]);
            }

            // 3. Resolve seat IDs for this screen
            $seats = $this->resolveSeats($showtime->screen_id, $seatIdentifiers);

            if ($seats->count() !== count($seatIdentifiers)) {
                throw ValidationException::withMessages([
                    'seats' => ['One or more selected seats do not exist in this cinema screen.'],
                ]);
            }

            $seatIds = $seats->pluck('id')->all();

            // 4. Lock existing booking seats for this showtime to check availability
            $takenSeats = BookingSeat::where('showtime_id', $showtime->id)
                ->whereIn('seat_id', $seatIds)
                ->lockForUpdate()
                ->pluck('seat_id')
                ->all();

            if (! empty($takenSeats)) {
                $takenLabels = $seats->whereIn('id', $takenSeats)->map(fn ($s) => "{$s->row_label}{$s->seat_number}")->join(', ');
                throw ValidationException::withMessages([
                    'seats' => ["The following seats are already taken: {$takenLabels}. Please select other seats."],
                ]);
            }

            // 5. Calculate total amount (standard price + $5 VIP surcharge if VIP seat)
            $totalAmount = 0.0;
            foreach ($seats as $seat) {
                $seatPrice = (float) $showtime->price;
                if ($seat->type === SeatType::VIP) {
                    $seatPrice += 5.0;
                }
                $totalAmount += $seatPrice;
            }

            // 6. Generate unique reference code
            $referenceCode = $this->generateReferenceCode();

            $expiresAt = null;
            if ($status === 'pending') {
                $expiresAt = now()->addMinutes(10);
            }

            // 7. Create Booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'showtime_id' => $showtime->id,
                'reference_code' => $referenceCode,
                'total_amount' => $totalAmount,
                'status' => $status === 'pending' ? BookingStatus::PENDING : BookingStatus::CONFIRMED,
                'expires_at' => $expiresAt,
            ]);

            // 8. Attach seats with unique constraint enforcement
            try {
                foreach ($seats as $seat) {
                    BookingSeat::create([
                        'booking_id' => $booking->id,
                        'seat_id' => $seat->id,
                        'showtime_id' => $showtime->id,
                    ]);
                }
            } catch (QueryException $e) {
                // If unique constraint (showtime_id, seat_id) is violated
                throw ValidationException::withMessages([
                    'seats' => ['One or more of the selected seats were just reserved by another customer. Please choose different seats.'],
                ]);
            }

            return $booking->load(['showtime.movie', 'showtime.screen.cinema', 'seats']);
        });
    }

    /**
     * Cancel a booking under business rules.
     *
     * @param  Booking  $booking
     * @return Booking
     *
     * @throws ValidationException
     */
    public function cancelBooking(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::where('id', $booking->id)
                ->with(['showtime'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status === BookingStatus::CANCELLED) {
                throw ValidationException::withMessages([
                    'booking' => ['This booking is already cancelled.'],
                ]);
            }

            // Business rule: Cancelling is only allowed up to 1 hour before showtime
            if (! $lockedBooking->canBeCancelled()) {
                throw ValidationException::withMessages([
                    'booking' => ['Bookings can only be cancelled up to 1 hour before showtime starts.'],
                ]);
            }

            // Free the reserved seats by removing the booking_seats rows
            BookingSeat::where('booking_id', $lockedBooking->id)->delete();

            // Mark booking as cancelled
            $lockedBooking->status = BookingStatus::CANCELLED;
            $lockedBooking->save();

            return $lockedBooking->fresh(['showtime.movie', 'showtime.screen.cinema', 'seats']);
        });
    }

    /**
     * Resolve given seat identifiers (either IDs or codes like "A1") to Seat models for a screen.
     */
    protected function resolveSeats(int $screenId, array $seatIdentifiers)
    {
        $numericIds = [];
        $codes = [];

        foreach ($seatIdentifiers as $ident) {
            if (is_numeric($ident)) {
                $numericIds[] = (int) $ident;
            } elseif (is_string($ident)) {
                $codes[] = strtoupper(trim($ident));
            }
        }

        return Seat::where('screen_id', $screenId)
            ->where(function ($query) use ($numericIds, $codes) {
                if (! empty($numericIds)) {
                    $query->orWhereIn('id', $numericIds);
                }

                foreach ($codes as $code) {
                    if (preg_match('/^([A-Z]+)(\d+)$/', $code, $matches)) {
                        $row = $matches[1];
                        $num = (int) $matches[2];
                        $query->orWhere(function ($q) use ($row, $num) {
                            $q->where('row_label', $row)->where('seat_number', $num);
                        });
                    }
                }
            })
            ->lockForUpdate()
            ->get();
    }

    /**
     * Generate a unique booking reference code (e.g. RT-A8B9C2).
     */
    protected function generateReferenceCode(): string
    {
        do {
            $code = 'RT'.strtoupper(Str::random(6));
        } while (Booking::where('reference_code', $code)->exists());

        return $code;
    }
}

