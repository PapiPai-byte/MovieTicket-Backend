<?php

namespace App\Models;

use App\Enums\ShowtimeStatus;
use Database\Factories\ShowtimeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    /** @use HasFactory<ShowtimeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'movie_id',
        'screen_id',
        'starts_at',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'price' => 'float',
            'status' => ShowtimeStatus::class,
        ];
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Check if showtime is in the past.
     */
    public function isPast(): bool
    {
        return $this->starts_at->isPast();
    }
}

