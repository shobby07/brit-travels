<x-mail::message>
# New Contact Message

| | |
|---|---|
| **Name** | {{ $data['name'] }} |
| **Email** | {{ $data['email'] }} |
@if (!empty($data['phone']))
| **Phone** | {{ $data['phone'] }} |
@endif

**Message:**

{{ $data['message'] }}

{{ config('app.name') }}
</x-mail::message>
