<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
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
            'movieId' => $this->movie_id,
            'movie_id' => $this->movie_id,
            'movie' => $this->whenLoaded('movie', fn () => new MovieResource($this->movie), $this->movie?->title),
            'cinema' => $this->screen?->cinema?->name ?? 'Cinema',
            'screen' => $this->screen?->name ?? 'Screen',
            'screen_id' => $this->screen_id,
            'startsAt' => $this->starts_at?->toIso8601String(),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'price' => (float) $this->price,
            'status' => is_string($this->status) ? $this->status : $this->status->value,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

