<x-mail::message>
# Hello {{ str($booking->name)->before(' ') }}, your booking request has been received.

Thank you for your booking request. Our team is currently reviewing the details and will confirm availability within 24 hours.

Your reference: **{{ $booking->reference }}**

@include('emails.partials.trip-details', ['trip' => $booking])

If any of the details above are incorrect, please reply to this email or call us on {{ \App\Models\Setting::get('phone') }} and we'll help straight away.

Safe travels,<br>
The {{ \App\Models\Setting::get('site_name', 'Brit Travel') }} Team
</x-mail::message>
