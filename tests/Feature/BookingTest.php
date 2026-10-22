<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Tests\TestCase;

class BookingTest extends TestCase
{
    protected User $user;
    protected Showtime $showtime;
    protected Screen $screen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $movie = Movie::factory()->create();
        $this->screen = Screen::factory()->create(['total_rows' => 4, 'seats_per_row' => 5]);
        $this->screen->generateSeats();

        $this->showtime = Showtime::factory()->create([
            'movie_id' => $movie->id,
            'screen_id' => $this->screen->id,
            'starts_at' => now()->addDays(2),
            'price' => 15.00,
        ]);
    }

    public function test_user_can_create_a_booking(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/bookings', [
            'showtime_id' => $this->showtime->id,
            'seats' => ['A1', 'A2'],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.seats_count', 2)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reference',
                    'movie',
                    'showtime',
                    'seats',
                    'total',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'showtime_id' => $this->showtime->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_user_cannot_book_more_than_8_seats(): void
    {
        $seats = ['A1', 'A2', 'A3', 'A4', 'A5', 'B1', 'B2', 'B3', 'B4']; // 9 seats

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/bookings', [
            'showtime_id' => $this->showtime->id,
            'seats' => $seats,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_user_cannot_book_past_showtimes(): void
    {
        $pastShowtime = Showtime::factory()->create([
            'screen_id' => $this->screen->id,
            'starts_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/bookings', [
            'showtime_id' => $pastShowtime->id,
            'seats' => ['A1'],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_user_can_cancel_booking_more_than_one_hour_before_showtime(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'showtime_id' => $this->showtime->id, // Starts in 2 days
            'status' => BookingStatus::CONFIRMED,
        ]);

        $seat = Seat::where('screen_id', $this->screen->id)->first();
        BookingSeat::create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'showtime_id' => $this->showtime->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);

        // Seat should be freed
        $this->assertDatabaseMissing('booking_seats', [
            'booking_id' => $booking->id,
        ]);
    }

    public function test_user_cannot_cancel_booking_less_than_one_hour_before_showtime(): void
    {
        $imminentShowtime = Showtime::factory()->create([
            'screen_id' => $this->screen->id,
            'starts_at' => now()->addMinutes(30), // Starts in 30 minutes!
        ]);

        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'showtime_id' => $imminentShowtime->id,
            'status' => BookingStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/bookings/{$booking->id}/cancel");

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }
}

