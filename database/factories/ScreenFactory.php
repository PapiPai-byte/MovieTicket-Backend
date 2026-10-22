<?php

namespace Database\Factories;

use App\Models\Cinema;
use App\Models\Screen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screen>
 */
class ScreenFactory extends Factory
{
    protected $model = Screen::class;

    public function definition(): array
    {
        return [
            'cinema_id' => Cinema::factory(),
            'name' => 'Screen '.fake()->numberBetween(1, 10),
            'total_rows' => 8,
            'seats_per_row' => 10,
            'type' => 'standard',
            'status' => 'active',
        ];
    }
}

