<?php

namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * A booking request.
 *
 * Not persisted — the site has no database. A request is built from the
 * submitted form, emailed to the office and to the customer, and then
 * discarded. The reference is generated so both emails can quote the same one.
 */
class Booking
{
    public ?Coach $coach = null;

    public function __construct(
        public string $reference = '',
        public ?string $trip_type = null,
        public ?string $pickup_location = null,
        public ?string $dropoff_location = null,
        public array $via_routes = [],
        public ?Carbon $pickup_date = null,
        public ?string $pickup_time = null,
        public ?Carbon $return_date = null,
        public ?string $return_time = null,
        public ?int $passengers = null,
        public int $luggage_small = 0,
        public int $bags_medium = 0,
        public int $luggage_large = 0,
        public ?int $coach_id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $notes = null,
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
            via_routes: $data['via_routes'] ?? [],
            pickup_date: ! empty($data['pickup_date']) ? Carbon::parse($data['pickup_date']) : null,
            pickup_time: $data['pickup_time'] ?? null,
            return_date: ! empty($data['return_date']) ? Carbon::parse($data['return_date']) : null,
            return_time: $data['return_time'] ?? null,
            passengers: isset($data['passengers']) ? (int) $data['passengers'] : null,
            luggage_small: (int) ($data['luggage_small'] ?? 0),
            bags_medium: (int) ($data['bags_medium'] ?? 0),
            luggage_large: (int) ($data['luggage_large'] ?? 0),
            coach_id: isset($data['coach_id']) && $data['coach_id'] !== null ? (int) $data['coach_id'] : null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public static function generateReference(): string
    {
        return 'BT-'.now()->format('Y').'-'.strtoupper(str()->random(5));
    }

    public function isRoundTrip(): bool
    {
        return $this->trip_type === 'round_trip';
    }

    /**
     * Human-readable luggage breakdown, e.g. "2 small · 1 medium · 3 large".
     * Returns null when nothing was declared, so callers can skip the row.
     */
    public function luggageSummary(): ?string
    {
        $parts = array_filter([
            $this->luggage_small ? "{$this->luggage_small} small" : null,
            $this->bags_medium ? "{$this->bags_medium} medium" : null,
            $this->luggage_large ? "{$this->luggage_large} large" : null,
        ]);

        return $parts ? implode(' · ', $parts) : null;
    }
}
