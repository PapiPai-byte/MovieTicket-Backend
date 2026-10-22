<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShowtimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movie_id' => ['nullable', 'integer', 'exists:movies,id'],
            'movie' => ['nullable'],
            'screen_id' => ['nullable', 'integer', 'exists:screens,id'],
            'screen' => ['nullable'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'startsAt' => ['nullable', 'date'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,cancelled,completed'],
        ];
    }
}

