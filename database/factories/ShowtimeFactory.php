<?php

namespace Database\Factories;

use App\Enums\ShowtimeStatus;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showtime>
 */
class ShowtimeFactory extends Factory
{
    protected $model = Showtime::class;

    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'screen_id' => Screen::factory(),
            'starts_at' => now()->addDays(1)->setTime(18, 0),
            'price' => 15.00,
            'status' => ShowtimeStatus::ACTIVE,
        ];
    }
}

