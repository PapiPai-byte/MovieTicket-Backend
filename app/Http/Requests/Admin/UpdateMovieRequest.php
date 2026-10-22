<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $movieId = $this->route('movie')?->id ?? $this->route('movie') ?? $this->route('id');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('movies', 'slug')->ignore($movieId)],
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

