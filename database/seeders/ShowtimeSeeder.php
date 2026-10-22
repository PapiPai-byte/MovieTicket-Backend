<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::where('status', 'now_showing')->get();
        $screens = Screen::all();

        if ($movies->isEmpty() || $screens->isEmpty()) {
            return;
        }

        $baseDate = now()->startOfDay();
        $timeSlots = ['11:30:00', '14:30:00', '17:30:00', '20:30:00'];

        $createdShowtimes = [];

        foreach ($movies as $movieIndex => $movie) {
            // Assign 1 or 2 screens for this movie
            $screen = $screens[$movieIndex % $screens->count()];

            for ($day = 0; $day < 3; $day++) {
                foreach ($timeSlots as $slotIndex => $timeSlot) {
                    $startsAt = $baseDate->copy()->addDays($day)->setTimeFromTimeString($timeSlot);

                    $showtime = Showtime::updateOrCreate(
                        [
                            'movie_id' => $movie->id,
                            'screen_id' => $screen->id,
                            'starts_at' => $startsAt,
                        ],
                        [
                            'price' => $slotIndex % 2 === 0 ? 14.50 : 17.00,
                            'status' => 'active',
                        ]
                    );

                    $createdShowtimes[] = $showtime;
                }
            }
        }

        // Create sample initial bookings for stats demonstration
        $sampleUser = User::where('email', 'user@example.com')->first();
        if ($sampleUser && ! empty($createdShowtimes)) {
            $targetShowtime = $createdShowtimes[0];
            $seats = Seat::where('screen_id', $targetShowtime->screen_id)->limit(2)->get();

            if ($seats->count() >= 2 && ! Booking::where('showtime_id', $targetShowtime->id)->exists()) {
                $booking = Booking::create([
                    'user_id' => $sampleUser->id,
                    'showtime_id' => $targetShowtime->id,
                    'reference_code' => 'RT'.strtoupper(Str::random(6)),
                    'total_amount' => $targetShowtime->price * 2,
                    'status' => BookingStatus::CONFIRMED,
                    'expires_at' => null,
                ]);

                foreach ($seats as $seat) {
                    BookingSeat::create([
                        'booking_id' => $booking->id,
                        'seat_id' => $seat->id,
                        'showtime_id' => $targetShowtime->id,
                    ]);
                }
            }
        }
    }
}

