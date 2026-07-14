<x-mail::message>
@if ($booking->status === \App\Models\Booking::STATUS_CONFIRMED)
# Your booking is confirmed! 🎉

Great news {{ str($booking->name)->before(' ') }} — your coach is booked. Here are your trip details:
@elseif ($booking->status === \App\Models\Booking::STATUS_CANCELLED)
# Your booking has been cancelled

Hi {{ str($booking->name)->before(' ') }}, your booking **{{ $booking->reference }}** has been cancelled. If this wasn't expected, please get in touch and we'll sort it out.
@else
# Booking update

Hi {{ str($booking->name)->before(' ') }}, the status of your booking **{{ $booking->reference }}** is now **{{ \App\Models\Booking::STATUSES[$booking->status] ?? $booking->status }}**.
@endif

Reference: **{{ $booking->reference }}**

@include('emails.partials.trip-details', ['trip' => $booking])

Questions? Reply to this email or call us on {{ \App\Models\Setting::get('phone') }}.

The {{ \App\Models\Setting::get('site_name', 'Brit Travels') }} Team
</x-mail::message>
