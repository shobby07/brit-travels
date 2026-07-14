<x-mail::message>
# Thanks {{ str($booking->name)->before(' ') }}, we've got your request!

Your booking request has been received and our team is reviewing it now. We'll confirm availability and get back to you shortly — usually within a few hours.

Your reference: **{{ $booking->reference }}**

@include('emails.partials.trip-details', ['trip' => $booking])

If anything above looks wrong, just reply to this email or call us on {{ \App\Models\Setting::get('phone') }} and quote your reference.

Safe travels,<br>
The {{ \App\Models\Setting::get('site_name', 'Brit Travels') }} Team
</x-mail::message>
