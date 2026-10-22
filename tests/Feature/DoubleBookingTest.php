<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DoubleBookingTest extends TestCase
{
    protected User $user1;
    protected User $user2;
    protected Showtime $showtime;
    protected Screen $screen;
    protected Seat $seat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $movie = Movie::factory()->create();
        $this->screen = Screen::factory()->create(['total_rows' => 2, 'seats_per_row' => 2]);
        $this->screen->generateSeats();

        $this->showtime = Showtime::factory()->create([
            'movie_id' => $movie->id,
            'screen_id' => $this->screen->id,
            'starts_at' => now()->addDays(2),
        ]);

        $this->seat = Seat::where('screen_id', $this->screen->id)->first();
    }

    public function test_two_users_cannot_book_the_same_seat(): void
    {
        $seatCode = "{$this->seat->row_label}{$this->seat->seat_number}";

        // First user books the seat
        $res1 = $this->actingAs($this->user1, 'sanctum')->postJson('/api/v1/bookings', [
            'showtime_id' => $this->showtime->id,
            'seats' => [$seatCode],
        ]);

        $res1->assertStatus(201)
            ->assertJsonPath('success', true);

        // Second user attempts to book the exact same seat for the same showtime
        $res2 = $this->actingAs($this->user2, 'sanctum')->postJson('/api/v1/bookings', [
            'showtime_id' => $this->showtime->id,
            'seats' => [$seatCode],
        ]);

        // Must fail with validation error and 422
        $res2->assertStatus(422)
            ->assertJsonPath('success', false);

        // Assert database only contains 1 booking seat record for this showtime and seat
        $this->assertEquals(
            1,
            BookingSeat::where('showtime_id', $this->showtime->id)
                ->where('seat_id', $this->seat->id)
                ->count()
        );
    }

    public function test_database_unique_constraint_enforces_single_booking_per_showtime_seat(): void
    {
        $booking1 = Booking::factory()->create([
            'user_id' => $this->user1->id,
            'showtime_id' => $this->showtime->id,
        ]);

        $booking2 = Booking::factory()->create([
            'user_id' => $this->user2->id,
            'showtime_id' => $this->showtime->id,
        ]);

        // Insert first booking seat directly
        BookingSeat::create([
            'booking_id' => $booking1->id,
            'seat_id' => $this->seat->id,
            'showtime_id' => $this->showtime->id,
        ]);

        // Second insert of same seat and showtime directly at DB level must throw QueryException
        $this->expectException(QueryException::class);

        BookingSeat::create([
            'booking_id' => $booking2->id,
            'seat_id' => $this->seat->id,
            'showtime_id' => $this->showtime->id,
        ]);
    }
}

