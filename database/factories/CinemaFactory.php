<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cinema>
 */
class CinemaFactory extends Factory
{
    protected $model = Cinema::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Cinema',
            'location' => fake()->address(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'status' => 'active',
        ];
    }
}

