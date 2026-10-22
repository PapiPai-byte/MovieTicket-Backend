<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $seatCodes = $this->seats->map(function ($seat) {
            return "{$seat->row_label}{$seat->seat_number}";
        })->values()->all();

        return [
            'id' => $this->id,
            'reference' => $this->reference_code,
            'reference_code' => $this->reference_code,
            'user_id' => $this->user_id,
            'movie' => $this->showtime?->movie ? new MovieResource($this->showtime->movie) : null,
            'showtime' => $this->showtime ? new ShowtimeResource($this->showtime) : null,
            'seats' => $seatCodes,
            'seats_count' => count($seatCodes),
            'total' => (float) $this->total_amount,
            'total_amount' => (float) $this->total_amount,
            'status' => is_string($this->status) ? $this->status : $this->status->value,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

