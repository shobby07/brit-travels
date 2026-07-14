<x-mail::message>
# Thanks {{ str($quote->name)->before(' ') }}, your quote is on its way!

We've received your quotation request and our team is pricing it up now. You'll usually hear back from us the same day.

Your reference: **{{ $quote->reference }}**

@include('emails.partials.trip-details', ['trip' => $quote])

If you'd like to add anything, just reply to this email and quote your reference.

Safe travels,<br>
The {{ \App\Models\Setting::get('site_name', 'Brit Travels') }} Team
</x-mail::message>
