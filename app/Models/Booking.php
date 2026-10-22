<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'showtime_id',
        'reference_code',
        'total_amount',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'float',
            'status' => BookingStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function seats(): HasManyThrough
    {
        return $this->hasManyThrough(
            Seat::class,
            BookingSeat::class,
            'booking_id',
            'id',
            'id',
            'seat_id'
        );
    }

    /**
     * Business rule: Cancelling is only allowed up to 1 hour before showtime.
     */
    public function canBeCancelled(): bool
    {
        if ($this->status !== BookingStatus::CONFIRMED && $this->status !== BookingStatus::PENDING) {
            return false;
        }

        if (! $this->showtime) {
            return false;
        }

        // Showtime starts_at must be at least 60 minutes in the future
        return now()->diffInMinutes($this->showtime->starts_at, false) >= 60;
    }
}

