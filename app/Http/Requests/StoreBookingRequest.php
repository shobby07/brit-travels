<?php

namespace App\Http\Requests;

use App\Models\Coach;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The luggage counts are optional on the form — an empty box means none,
     * so normalise blanks to 0 rather than failing validation or writing null
     * into a NOT NULL column.
     */
    protected function prepareForValidation(): void
    {
        foreach (['luggage_small', 'bags_medium', 'luggage_large'] as $field) {
            if ($this->input($field) === null || $this->input($field) === '') {
                $this->merge([$field => 0]);
            }
        }

        // Drop blank via-stop rows the visitor added but never filled in, and
        // reindex so the stored list has no gaps.
        if (is_array($this->input('via_routes'))) {
            $this->merge([
                'via_routes' => array_values(array_filter(
                    array_map(fn ($stop) => is_string($stop) ? trim($stop) : $stop, $this->input('via_routes')),
                    fn ($stop) => is_string($stop) && $stop !== '',
                )),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'trip_type' => ['required', 'in:one_way,round_trip'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'via_routes' => ['nullable', 'array', 'max:10'],
            'via_routes.*' => ['required', 'string', 'max:255'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'return_date' => ['required_if:trip_type,round_trip', 'nullable', 'date', 'after_or_equal:pickup_date'],
            'return_time' => ['required_if:trip_type,round_trip', 'nullable', 'date_format:H:i'],
            'passengers' => ['required', 'integer', 'min:1', 'max:500'],
            'luggage_small' => ['integer', 'min:0', 'max:999'],
            'bags_medium' => ['integer', 'min:0', 'max:999'],
            'luggage_large' => ['integer', 'min:0', 'max:999'],
            'coach_id' => ['nullable', Rule::in(Coach::ids())],
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
            'via_routes.max' => 'You can add up to 10 via stops.',
            'via_routes.*.required' => 'Please fill in or remove the empty via stop.',
            'return_date.required_if' => 'Please choose a return date for a round trip.',
            'return_time.required_if' => 'Please choose a return time for a round trip.',
            'return_date.after_or_equal' => 'The return date must be on or after the pickup date.',
            'website.prohibited' => 'Submission rejected.',
        ];
    }
}
