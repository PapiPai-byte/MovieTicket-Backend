<?php

namespace App\Models;

use App\Enums\SeatType;
use Database\Factories\SeatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    /** @use HasFactory<SeatFactory> */
    use HasFactory;

    protected $fillable = [
        'screen_id',
        'row_label',
        'seat_number',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'seat_number' => 'integer',
            'type' => SeatType::class,
        ];
    }

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Get the alphanumeric seat code (e.g., A1, B10).
     */
    public function getCodeAttribute(): string
    {
        return "{$this->row_label}{$this->seat_number}";
    }
}

