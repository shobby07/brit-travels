@php
    $introParagraphs = $location->intro_content
        ? preg_split('/\n\n+/', trim($location->intro_content))
        : [];

    $serviceJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Coach Hire '.$location->name,
        'serviceType' => 'Coach and minibus hire',
        'description' => $location->meta_description ?: ($introParagraphs[0] ?? null),
        'provider' => ['@id' => url('/').'#organization'],
        'areaServed' => [
            '@type' => 'City',
            'name' => $location->name,
        ],
        'url' => route('coach-hire.show', $location),
    ];

    $breadcrumbJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Coach Hire', 'item' => route('coach-hire.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Coach Hire '.$location->name, 'item' => route('coach-hire.show', $location)],
        ],
    ];

    $faqJsonLd = ! empty($location->faqs) ? [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($location->faqs)->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq['question'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'] ?? '',
            ],
        ])->all(),
    ] : null;
@endphp

<x-layout
    :title="$location->meta_title ?? 'Coach Hire '.$location->name"
    :description="$location->meta_description ?? 'Coach and minibus hire in '.$location->name.' with professional drivers. Airport transfers, event travel, weddings and day trips. Free instant quote from Brit Travel.'"
    :canonical="route('coach-hire.show', $location)"
>
    <x-slot:head>
        <script type="application/ld+json">{!! json_encode($serviceJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @if ($faqJsonLd)
            <script type="application/ld+json">{!! json_encode($faqJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
    </x-slot:head>

    <x-page-header
        :eyebrow="'Coach Hire · '.$location->name"
        :title="$location->intro_heading ?? 'Coach Hire '.$location->name"
        :image="$location->hero_image"
        :imageAlt="$location->hero_image_alt ?? ('Coach hire in '.$location->name)"
    >
        <div class="mt-8 flex flex-wrap gap-4" data-hero-reveal>
            <a href="#book" class="btn-primary">Get a Quote for {{ $location->name }}</a>
            <a href="{{ route('fleet.index') }}" class="btn-ghost-light">View the Fleet</a>
        </div>
    </x-page-header>

    {{-- Article (left) + sticky quote form (right) --}}
    <section class="py-16 sm:py-24">
        <div class="container-site grid gap-10 lg:grid-cols-[1.6fr_1fr] lg:gap-14">

            {{-- LEFT: article --}}
            <div>
                {{-- On this page --}}
                <nav class="gsap-reveal mb-10 rounded-2xl border border-navy-100 bg-navy-50/60 p-5" aria-label="On this page">
                    <p class="section-eyebrow mb-2">On this page</p>
                    <div class="flex flex-wrap gap-2">
                        @if (! empty($location->why_choose_points))
                            <a href="#why-choose" class="rounded-full border border-navy-200 bg-white px-4 py-1.5 text-xs font-semibold text-navy-700 transition-colors hover:border-navy-900 hover:text-navy-950">Why choose us</a>
                        @endif
                        <a href="#book" class="rounded-full border border-navy-200 bg-white px-4 py-1.5 text-xs font-semibold text-navy-700 transition-colors hover:border-navy-900 hover:text-navy-950">Get an instant quote</a>
                        @if (! empty($location->faqs))
                            <a href="#faqs" class="rounded-full border border-navy-200 bg-white px-4 py-1.5 text-xs font-semibold text-navy-700 transition-colors hover:border-navy-900 hover:text-navy-950">FAQs</a>
                        @endif
                        <a href="{{ route('fleet.index') }}" class="rounded-full border border-navy-200 bg-white px-4 py-1.5 text-xs font-semibold text-navy-700 transition-colors hover:border-navy-900 hover:text-navy-950">Our fleet</a>
                    </div>
                </nav>

                {{-- Intro --}}
                <div class="gsap-reveal">
                    @foreach ($introParagraphs as $paragraph)
                        <p class="{{ $loop->first ? 'text-lg text-navy-800' : 'mt-5 text-navy-600' }} leading-relaxed">{{ $paragraph }}</p>
                    @endforeach

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('booking.create', ['pickup_location' => $location->name]) }}" class="btn-primary">Book Coach Hire in {{ $location->name }}</a>
                        <a href="{{ route('quote.create', ['pickup_location' => $location->name]) }}" class="btn-secondary">Get a Free Quote</a>
                    </div>
                </div>

                {{-- Why choose us --}}
                @if (! empty($location->why_choose_points))
                    <div id="why-choose" class="mt-16 scroll-mt-28">
                        <div class="gsap-reveal">
                            <p class="section-eyebrow">Why Brit Travel</p>
                            <h2 class="font-display text-2xl font-semibold tracking-tight text-navy-950 sm:text-3xl">Coach hire in {{ $location->name }}, done properly</h2>
                        </div>
                        <div class="mt-8 grid gap-5 sm:grid-cols-2 gsap-stagger">
                            @foreach ($location->why_choose_points as $point)
                                <div class="rounded-3xl border border-navy-100 bg-white p-7">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </span>
                                    <h3 class="mt-5 font-display text-lg font-semibold text-navy-950">{{ $point['title'] ?? '' }}</h3>
                                    @if (! empty($point['description']))
                                        <p class="mt-2.5 text-sm leading-relaxed text-navy-600">{{ $point['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- FAQs --}}
                @if (! empty($location->faqs))
                    <div id="faqs" class="mt-16 scroll-mt-28 gsap-reveal">
                        <p class="section-eyebrow">FAQs</p>
                        <h2 class="font-display text-2xl font-semibold tracking-tight text-navy-950 sm:text-3xl">{{ $location->name }} coach hire questions</h2>
                        <p class="mt-3 text-sm leading-relaxed text-navy-600">Can't see your question? <a href="{{ route('contact') }}" class="font-semibold text-accent-600 hover:underline">Get in touch</a> and we'll help.</p>
                        <div class="mt-6 space-y-4 gsap-stagger">
                            @foreach ($location->faqs as $faq)
                                <details class="group rounded-2xl border border-navy-100 bg-white px-6 py-5 [&_summary::-webkit-details-marker]:hidden">
                                    <summary class="flex cursor-pointer items-center justify-between gap-4 font-display text-[15px] font-semibold text-navy-950">
                                        {{ $faq['question'] ?? '' }}
                                        <svg class="h-5 w-5 shrink-0 text-accent-500 transition-transform duration-300 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                                    </summary>
                                    <p class="mt-3 text-sm leading-relaxed text-navy-600">{{ $faq['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Other locations (internal linking) --}}
                @if ($others->isNotEmpty())
                    <div class="mt-16 gsap-reveal">
                        <h2 class="font-display text-2xl font-semibold tracking-tight text-navy-950 sm:text-3xl">Coach hire in other cities</h2>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @foreach ($others as $other)
                                <a href="{{ route('coach-hire.show', $other) }}" class="rounded-full border border-navy-200 bg-white px-5 py-2.5 text-sm font-semibold text-navy-800 transition-colors hover:border-navy-900 hover:text-navy-950">Coach Hire {{ $other->name }}</a>
                            @endforeach
                            <a href="{{ route('coach-hire.index') }}" class="rounded-full bg-navy-950 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-navy-800">View all locations</a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT: sticky quote form (inline on mobile, sticky from lg up) --}}
            <aside id="book" class="scroll-mt-28">
                <div class="gsap-reveal lg:sticky lg:top-24" data-reveal="right">
                    <div class="mb-5">
                        <p class="section-eyebrow">Instant Quote</p>
                        <h2 class="font-display text-2xl font-semibold tracking-tight text-navy-950">Book coach hire in {{ $location->name }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-navy-600">We've set {{ $location->name }} as your pickup to save you a step — change it any time.</p>
                    </div>
                    <x-booking-form :coaches="$coaches" :pickup="$location->name" :compact="true" />
                </div>
            </aside>
        </div>
    </section>

    {{-- CTA band --}}
    <section class="relative overflow-hidden bg-navy-950 py-20 sm:py-24">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/2 top-1/2 h-96 w-160 -translate-x-1/2 -translate-y-1/2 rounded-full bg-accent-500/10 blur-3xl"></div>
        </div>
        <div class="container-site relative text-center gsap-reveal">
            <h2 class="mx-auto max-w-2xl font-display text-3xl font-semibold tracking-tight text-white sm:text-4xl lg:text-5xl">Ready to book coach hire in {{ $location->name }}?</h2>
            <p class="mx-auto mt-4 max-w-xl text-white/60">Free, no-obligation quotes — usually the same day. Professional drivers, modern coaches, and effortless booking.</p>
            <div class="mt-9 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.create', ['pickup_location' => $location->name]) }}" class="btn-primary">Book a Coach</a>
                <a href="{{ route('quote.create', ['pickup_location' => $location->name]) }}" class="btn-ghost-light">Get a Free Quote</a>
            </div>
        </div>
    </section>
</x-layout>
