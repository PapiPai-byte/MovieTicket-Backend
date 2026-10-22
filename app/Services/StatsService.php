<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Http\Resources\V1\MovieResource;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;

class StatsService
{
    /**
     * Get dashboard statistics for admin.
     *
     * @return array<string, mixed>
     */
    public function getDashboardStats(): array
    {
        // 1. Total revenue from confirmed bookings
        $totalRevenue = (float) Booking::where('status', BookingStatus::CONFIRMED->value)
            ->sum('total_amount');

        // 2. Total tickets sold (seats booked across confirmed bookings)
        $ticketsSold = BookingSeat::whereHas('booking', function ($q) {
            $q->where('status', BookingStatus::CONFIRMED->value);
        })->count();

        // 3. Top movies by tickets sold
        $topMovieIds = BookingSeat::whereHas('booking', function ($q) {
            $q->where('status', BookingStatus::CONFIRMED->value);
        })
            ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
            ->select('showtimes.movie_id', DB::raw('COUNT(booking_seats.id) as tickets_count'))
            ->groupBy('showtimes.movie_id')
            ->orderByDesc('tickets_count')
            ->limit(5)
            ->pluck('movie_id');

        $topMovies = Movie::whereIn('id', $topMovieIds)->get();

        // If no bookings yet or less than 3, backfill with active movies
        if ($topMovies->count() < 3) {
            $existingIds = $topMovies->pluck('id')->all();
            $fillers = Movie::whereNotIn('id', $existingIds)
                ->where('status', 'now_showing')
                ->limit(3 - $topMovies->count())
                ->get();
            $topMovies = $topMovies->merge($fillers);
        }

        return [
            'revenue' => $totalRevenue,
            'total_revenue' => $totalRevenue,
            'tickets' => $ticketsSold,
            'tickets_sold' => $ticketsSold,
            'topMovies' => MovieResource::collection($topMovies),
            'top_movies' => MovieResource::collection($topMovies),
        ];
    }
}

