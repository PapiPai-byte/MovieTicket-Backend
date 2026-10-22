<?php

namespace App\Models;

use Database\Factories\CinemaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    /** @use HasFactory<CinemaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'address',
        'city',
        'status',
    ];

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}

