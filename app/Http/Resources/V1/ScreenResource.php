<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cinema_id' => $this->cinema_id,
            'cinema' => $this->cinema?->name,
            'name' => $this->name,
            'total_rows' => $this->total_rows,
            'seats_per_row' => $this->seats_per_row,
            'capacity' => $this->total_rows * $this->seats_per_row,
            'type' => $this->type ?? 'standard',
            'status' => $this->status ?? 'active',
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

