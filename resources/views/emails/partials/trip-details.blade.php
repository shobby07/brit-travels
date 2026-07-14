| | |
|---|---|
| **Trip type** | {{ $trip->trip_type === 'round_trip' ? 'Round trip' : 'One way' }} |
| **Pickup** | {{ $trip->pickup_location }} |
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
@if ($trip->coach)
| **Coach** | {{ $trip->coach->name }} ({{ $trip->coach->seats }} seats) |
@endif
| **Name** | {{ $trip->name }} |
| **Email** | {{ $trip->email }} |
| **Phone** | {{ $trip->phone }} |
