<x-mail::message>
# New Quotation Request

Reference: **{{ $quote->reference }}**

@include('emails.partials.trip-details', ['trip' => $quote])

@if ($quote->message)
**Customer message:**
{{ $quote->message }}
@endif

<x-mail::button :url="url('/admin/quotes')">
Open Admin Dashboard
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
