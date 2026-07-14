<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_type' => ['nullable', 'in:one_way,round_trip'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'return_date' => ['nullable', 'date', 'after_or_equal:pickup_date'],
            'return_time' => ['nullable', 'date_format:H:i'],
            'passengers' => ['nullable', 'integer', 'min:1', 'max:500'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['prohibited'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_date.after_or_equal' => 'The pickup date cannot be in the past.',
            'return_date.after_or_equal' => 'The return date must be on or after the pickup date.',
            'website.prohibited' => 'Submission rejected.',
        ];
    }
}
