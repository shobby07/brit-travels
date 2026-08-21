<x-mail::message>
# New Booking Request

Reference: **{{ $booking->reference }}**

@include('emails.partials.trip-details', ['trip' => $booking])

@if ($booking->notes)
**Customer notes:**
{{ $booking->notes }}
@endif

Reply to this email to reach {{ $booking->name }} directly.

{{ config('app.name') }}
</x-mail::message>
