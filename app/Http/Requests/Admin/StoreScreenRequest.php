<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScreenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cinema_id' => ['nullable', 'integer', 'exists:cinemas,id'],
            'cinema' => ['nullable'], // Admin form might send cinema name or ID
            'name' => ['required', 'string', 'max:255'],
            'total_rows' => ['nullable', 'integer', 'min:1', 'max:26'],
            'seats_per_row' => ['nullable', 'integer', 'min:1', 'max:50'],
            'capacity' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }
}

