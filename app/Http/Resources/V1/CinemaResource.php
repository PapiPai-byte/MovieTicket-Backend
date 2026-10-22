<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CinemaResource extends JsonResource
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
            'name' => $this->name,
            'location' => $this->location,
            'address' => $this->address ?? $this->location,
            'city' => $this->city ?? '',
            'status' => $this->status ?? 'active',
            'screens_count' => $this->whenCounted('screens'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

