<?php

namespace Database\Factories;

use App\Enums\SeatType;
use App\Models\Screen;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition(): array
    {
        return [
            'screen_id' => Screen::factory(),
            'row_label' => fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']),
            'seat_number' => fake()->numberBetween(1, 10),
            'type' => SeatType::STANDARD,
        ];
    }
}

