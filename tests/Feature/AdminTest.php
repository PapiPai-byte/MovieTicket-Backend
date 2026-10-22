<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_non_admin_user_cannot_access_admin_routes(): void
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser, 'sanctum')->getJson('/api/v1/admin/stats');

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_access_dashboard_stats(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/stats');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'revenue',
                    'tickets',
                    'topMovies',
                ],
            ]);
    }

    public function test_admin_can_create_a_movie(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/movies', [
            'title' => 'Interstellar 2',
            'synopsis' => 'The journey continues.',
            'genre' => 'Sci-Fi, Adventure',
            'duration' => 165,
            'rating' => 8.9,
            'status' => 'now_showing',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Interstellar 2');

        $this->assertDatabaseHas('movies', [
            'title' => 'Interstellar 2',
        ]);
    }

    public function test_admin_can_delete_a_movie(): void
    {
        $admin = User::factory()->admin()->create();
        $movie = Movie::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/admin/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('movies', [
            'id' => $movie->id,
        ]);
    }
}

