<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowtimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movie_id' => ['nullable', 'integer', 'exists:movies,id'],
            'movie' => ['nullable'], // ID or title from admin form
            'screen_id' => ['nullable', 'integer', 'exists:screens,id'],
            'screen' => ['nullable'], // ID or name from admin form
            'cinema' => ['nullable'],
            'starts_at' => ['required', 'date'],
            'startsAt' => ['nullable', 'date'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,cancelled,completed'],
        ];
    }
}

