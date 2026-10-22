<?php

namespace App\Models;

use Database\Factories\ScreenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screen extends Model
{
    /** @use HasFactory<ScreenFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cinema_id',
        'name',
        'total_rows',
        'seats_per_row',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'seats_per_row' => 'integer',
        ];
    }

    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Auto-generate seats for this screen if none exist.
     */
    public function generateSeats(): void
    {
        $rows = range('A', chr(64 + min($this->total_rows, 26)));
        $seatsData = [];
        $now = now();

        foreach ($rows as $rowIndex => $rowLabel) {
            $isVip = $rowIndex >= max(0, $this->total_rows - 2); // Top 2 back rows are VIP

            for ($seatNum = 1; $seatNum <= $this->seats_per_row; $seatNum++) {
                $seatsData[] = [
                    'screen_id' => $this->id,
                    'row_label' => $rowLabel,
                    'seat_number' => $seatNum,
                    'type' => $isVip ? 'vip' : 'standard',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($seatsData)) {
            Seat::upsert($seatsData, ['screen_id', 'row_label', 'seat_number'], ['type', 'updated_at']);
        }
    }
}

