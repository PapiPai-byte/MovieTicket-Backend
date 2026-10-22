<?php

namespace App\Models;

use App\Enums\MovieStatus;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Movie extends Model
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'genre',
        'duration_minutes',
        'rating',
        'poster_url',
        'trailer_url',
        'release_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'genre' => 'array',
            'duration_minutes' => 'integer',
            'rating' => 'float',
            'release_date' => 'date',
            'status' => MovieStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Movie $movie) {
            if (empty($movie->slug)) {
                $baseSlug = Str::slug($movie->title);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $movie->slug = $slug;
            }
        });
    }

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'ilike', "%{$search}%")
              ->orWhere('synopsis', 'ilike', "%{$search}%");
        });
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeWithGenre(Builder $query, ?string $genre): Builder
    {
        if (empty($genre)) {
            return $query;
        }

        return $query->whereJsonContains('genre', $genre)
                     ->orWhere('genre', 'like', "%{$genre}%");
    }
}

