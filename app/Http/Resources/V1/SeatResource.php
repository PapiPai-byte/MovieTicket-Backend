<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
{
    /**
     * Additional data: whether this seat is taken for the requested showtime.
     */
    protected bool $isTaken;

    public function __construct($resource, bool $isTaken = false)
    {
        parent::__construct($resource);
        $this->isTaken = $isTaken;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $code = "{$this->row_label}{$this->seat_number}";

        return [
            'id' => $code, // Code e.g. "A1" for frontend seat map compatibility
            'seat_id' => $this->id,
            'row' => $this->row_label,
            'row_label' => $this->row_label,
            'number' => $this->seat_number,
            'seat_number' => $this->seat_number,
            'type' => is_string($this->type) ? $this->type : $this->type->value,
            'status' => $this->isTaken ? 'taken' : 'available',
        ];
    }
}

