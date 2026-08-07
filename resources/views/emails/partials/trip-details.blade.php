| | |
|---|---|
| **Trip type** | {{ $trip->trip_type === 'round_trip' ? 'Round trip' : 'One way' }} |
| **Pickup** | {{ $trip->pickup_location }} |
{{-- Shared with quotes, which have no via stops — the null-safe check keeps that working. --}}
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
{{-- Shared with quotes, which have no luggage fields — method_exists keeps this safe. --}}
@if (method_exists($trip, 'luggageSummary') && $trip->luggageSummary())
| **Luggage** | {{ $trip->luggageSummary() }} |
@endif
@if ($trip->coach)
| **Coach** | {{ $trip->coach->name }} ({{ $trip->coach->seats }} seats) |
@endif
| **Name** | {{ $trip->name }} |
| **Email** | {{ $trip->email }} |
| **Phone** | {{ $trip->phone }} |
