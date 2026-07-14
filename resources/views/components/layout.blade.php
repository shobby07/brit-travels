@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'ogImage' => null,
])

@php
    $siteName = setting('site_name', 'Brit Travels');
    $pageTitle = $title ? "{$title} | {$siteName}" : "{$siteName} | Coach Hire & Minibus Hire Across the UK";
    $pageDescription = $description ?? 'Premium coach and minibus hire across the UK. Modern fleet from 8 to 70 seats, professional drivers, and instant online booking with Brit Travels.';
    $pageCanonical = $canonical ?? url()->current();
@endphp

<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageCanonical }}">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">
    <meta property="og:locale" content="en_GB">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    {{-- LocalBusiness structured data (sitewide) --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "{{ url('/') }}#organization",
        "name": "{{ $siteName }}",
        "description": "Coach hire and minibus hire with professional drivers across the United Kingdom.",
        "url": "{{ url('/') }}",
        "telephone": "{{ setting('phone') }}",
        "email": "{{ setting('email') }}",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "GB"
        },
        "areaServed": {
            "@type": "Country",
            "name": "United Kingdom"
        },
        "priceRange": "££"
    }
    </script>

    {{ $head ?? '' }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <x-navbar />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer />
</body>
</html>
