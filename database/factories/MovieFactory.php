<?php

namespace Database\Factories;

use App\Enums\MovieStatus;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'synopsis' => fake()->paragraph(),
            'genre' => ['Action', 'Sci-Fi'],
            'duration_minutes' => fake()->numberBetween(90, 180),
            'rating' => fake()->randomFloat(1, 6.0, 9.5),
            'poster_url' => 'https://image.tmdb.org/t/p/w780/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
            'trailer_url' => 'https://www.youtube.com/embed/Way9Dexny3w',
            'release_date' => now()->subDays(10)->toDateString(),
            'status' => MovieStatus::NOW_SHOWING,
        ];
    }
}

