<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_type' => ['required', 'in:one_way,round_trip'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'return_date' => ['required_if:trip_type,round_trip', 'nullable', 'date', 'after_or_equal:pickup_date'],
            'return_time' => ['required_if:trip_type,round_trip', 'nullable', 'date_format:H:i'],
            'passengers' => ['required', 'integer', 'min:1', 'max:500'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'website' => ['prohibited'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_date.after_or_equal' => 'The pickup date cannot be in the past.',
            'return_date.required_if' => 'Please choose a return date for a round trip.',
            'return_time.required_if' => 'Please choose a return time for a round trip.',
            'return_date.after_or_equal' => 'The return date must be on or after the pickup date.',
            'website.prohibited' => 'Submission rejected.',
        ];
    }
}
