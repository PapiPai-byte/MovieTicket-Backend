<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'seats' => ['required', 'array', 'min:1', 'max:8'],
            'seats.*' => ['required'],
        ];
    }

    /**
     * Custom messages for validation.
     */
    public function messages(): array
    {
        return [
            'seats.max' => 'You can select up to 8 seats per booking.',
            'seats.min' => 'Please select at least one seat to book.',
        ];
    }
}

