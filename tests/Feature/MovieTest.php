<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function test_can_list_movies(): void
    {
        Movie::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'slug', 'genre', 'duration', 'rating', 'poster'],
                    ],
                    'current_page',
                    'last_page',
                    'total',
                ],
            ]);
    }

    public function test_can_get_movie_by_slug(): void
    {
        $movie = Movie::factory()->create(['slug' => 'test-movie-slug']);

        $response = $this->getJson('/api/v1/movies/test-movie-slug');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', 'test-movie-slug');
    }

    public function test_can_get_showtimes_and_seats(): void
    {
        $movie = Movie::factory()->create(['slug' => 'awesome-film']);
        $screen = Screen::factory()->create(['total_rows' => 3, 'seats_per_row' => 4]);
        $screen->generateSeats();

        $showtime = Showtime::factory()->create([
            'movie_id' => $movie->id,
            'screen_id' => $screen->id,
            'starts_at' => now()->addDays(1),
        ]);

        // Get showtimes
        $showtimeResponse = $this->getJson('/api/v1/movies/awesome-film/showtimes');
        $showtimeResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        // Get seat map
        $seatsResponse = $this->getJson("/api/v1/showtimes/{$showtime->id}/seats");
        $seatsResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(12, 'data'); // 3 rows * 4 seats
    }

    public function test_health_check_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'healthy')
            ->assertJsonPath('services.database', 'ok');
    }
}

