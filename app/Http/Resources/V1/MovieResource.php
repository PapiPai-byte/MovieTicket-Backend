<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $genres = is_array($this->genre) ? $this->genre : (is_string($this->genre) ? json_decode($this->genre, true) ?? explode(',', $this->genre) : []);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'genre' => array_values(array_filter(array_map('trim', (array) $genres))),
            'duration' => $this->duration_minutes,
            'duration_minutes' => $this->duration_minutes,
            'rating' => (float) $this->rating,
            'releaseDate' => $this->release_date?->format('Y-m-d'),
            'release_date' => $this->release_date?->format('Y-m-d'),
            'status' => is_string($this->status) ? $this->status : $this->status->value,
            'poster' => $this->poster_url,
            'poster_url' => $this->poster_url,
            'backdrop' => $this->poster_url,
            'trailer' => $this->trailer_url,
            'trailer_url' => $this->trailer_url,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

