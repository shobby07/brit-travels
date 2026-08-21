<?php

namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * A quotation request.
 *
 * Not persisted — see the note on Booking. Built from the submitted form,
 * emailed, then discarded.
 */
class Quote
{
    public ?Coach $coach = null;

    public function __construct(
        public string $reference = '',
        public ?string $trip_type = null,
        public ?string $pickup_location = null,
        public ?string $dropoff_location = null,
        public ?Carbon $pickup_date = null,
        public ?string $pickup_time = null,
        public ?Carbon $return_date = null,
        public ?string $return_time = null,
        public ?int $passengers = null,
        public ?int $coach_id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $message = null,
    ) {
        $this->coach = Coach::findById($this->coach_id);
    }

    /** Build from validated form input. */
    public static function fromArray(array $data): static
    {
        return new static(
            reference: $data['reference'] ?? static::generateReference(),
            trip_type: $data['trip_type'] ?? null,
            pickup_location: $data['pickup_location'] ?? null,
            dropoff_location: $data['dropoff_location'] ?? null,
            pickup_date: ! empty($data['pickup_date']) ? Carbon::parse($data['pickup_date']) : null,
            pickup_time: $data['pickup_time'] ?? null,
            return_date: ! empty($data['return_date']) ? Carbon::parse($data['return_date']) : null,
            return_time: $data['return_time'] ?? null,
            passengers: isset($data['passengers']) && $data['passengers'] !== null ? (int) $data['passengers'] : null,
            coach_id: isset($data['coach_id']) && $data['coach_id'] !== null ? (int) $data['coach_id'] : null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            message: $data['message'] ?? null,
        );
    }

    public static function generateReference(): string
    {
        return 'QT-'.now()->format('Y').'-'.strtoupper(str()->random(5));
    }

    public function isRoundTrip(): bool
    {
        return $this->trip_type === 'round_trip';
    }
}
