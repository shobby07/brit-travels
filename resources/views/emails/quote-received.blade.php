<x-mail::message>
# New Quotation Request

Reference: **{{ $quote->reference }}**

@include('emails.partials.trip-details', ['trip' => $quote])

@if ($quote->message)
**Customer message:**
{{ $quote->message }}
@endif

Reply to this email to reach {{ $quote->name }} directly.

{{ config('app.name') }}
</x-mail::message>
