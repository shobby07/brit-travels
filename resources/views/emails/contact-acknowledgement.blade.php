<x-mail::message>
# Thanks {{ str($data['name'])->before(' ') }}, we've got your message!

It's landed with our team and we'll get back to you within 24 hours.

If it's urgent, call us on {{ \App\Models\Setting::get('phone') }} and we'll help straight away.

**What you sent us:**

{{-- Rendered as a plain block rather than a blockquote: a multi-line message
     breaks out of a "> " quote at its first blank line, leaving half the text
     unquoted. The office copy renders it the same way. --}}
{{ $data['message'] }}

Safe travels,<br>
The {{ \App\Models\Setting::get('site_name', 'Brit Travel') }} Team
</x-mail::message>
