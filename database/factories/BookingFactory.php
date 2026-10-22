<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'showtime_id' => Showtime::factory(),
            'reference_code' => 'RT'.strtoupper(Str::random(6)),
            'total_amount' => 30.00,
            'status' => BookingStatus::CONFIRMED,
            'expires_at' => null,
        ];
    }
}

