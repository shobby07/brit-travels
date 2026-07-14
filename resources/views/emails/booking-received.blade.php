<x-mail::message>
# New Booking Request

Reference: **{{ $booking->reference }}**

@include('emails.partials.trip-details', ['trip' => $booking])

@if ($booking->notes)
**Customer notes:**
{{ $booking->notes }}
@endif

<x-mail::button :url="url('/admin/bookings')">
Open Admin Dashboard
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
