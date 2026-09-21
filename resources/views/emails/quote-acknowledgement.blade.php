<x-mail::message>
# Thanks {{ str($quote->name)->before(' ') }}, your quote is on its way!

We've received your quotation request and our team is pricing it up now. You'll hear back from us within 24 hours.

Your reference: **{{ $quote->reference }}**

@include('emails.partials.trip-details', ['trip' => $quote])

If you'd like to add anything, just reply to this email and quote your reference. In a hurry? Call us on {{ \App\Models\Setting::get('phone') }} and we'll price it up over the phone.

Safe travels,<br>
The {{ \App\Models\Setting::get('site_name', 'Brit Travel') }} Team
</x-mail::message>
