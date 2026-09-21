{{--
    The trip summary table, shared by the booking and quote emails (both the
    office notification and the customer's copy).

    Keep every comment above the table. A Blade comment is stripped but the
    newline it sat on is not, and a blank line ends a Markdown table — which
    silently dropped every row below it, including the customer's name, email
    and phone, out of the table and into raw "| **Name** | Jane |" pipe text.
    For the same reason, notes about a row go at the end of its @if line.

    Rows are conditional because quotes carry fewer fields than bookings:
    quotes have no via stops and no luggage counts.
--}}
| | |
|---|---|
| **Trip type** | {{ $trip->trip_type === 'round_trip' ? 'Round trip' : 'One way' }} |
| **Pickup** | {{ $trip->pickup_location }} |
@if (! empty($trip->via_routes))
| **Via** | {{ implode(' → ', (array) $trip->via_routes) }} |
@endif
| **Drop-off** | {{ $trip->dropoff_location }} |
@if ($trip->pickup_date)
| **Pickup date** | {{ $trip->pickup_date->format('l j F Y') }} at {{ \Illuminate\Support\Str::of($trip->pickup_time)->limit(5, '') }} |
@endif
@if ($trip->return_date)
| **Return** | {{ $trip->return_date->format('l j F Y') }}@if ($trip->return_time) at {{ \Illuminate\Support\Str::of($trip->return_time)->limit(5, '') }}@endif |
@endif
@if ($trip->passengers)
| **Passengers** | {{ $trip->passengers }} |
@endif
@if (method_exists($trip, 'luggageSummary') && $trip->luggageSummary())
| **Luggage** | {{ $trip->luggageSummary() }} |
@endif
@if ($trip->coach)
| **Coach** | {{ $trip->coach->name }} ({{ $trip->coach->seats }} seats) |
@endif
| **Name** | {{ $trip->name }} |
| **Email** | {{ $trip->email }} |
| **Phone** | {{ $trip->phone }} |
