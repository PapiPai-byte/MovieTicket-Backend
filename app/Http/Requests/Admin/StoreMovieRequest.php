<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:movies,slug'],
            'synopsis' => ['nullable', 'string'],
            'genre' => ['nullable'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'poster' => ['nullable', 'string'],
            'poster_url' => ['nullable', 'string'],
            'trailer' => ['nullable', 'string'],
            'trailer_url' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'releaseDate' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:now_showing,coming_soon,archived'],
        ];
    }
}

