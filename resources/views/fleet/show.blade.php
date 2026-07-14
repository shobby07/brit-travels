<x-layout
    :title="$coach->meta_title ?? $coach->name.' Hire UK'"
    :description="$coach->meta_description ?? 'Hire a '.$coach->seats.' seater coach with a professional driver anywhere in the UK. Free instant quote from Brit Travels.'"
>
    <x-slot:head>
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "Service",
            "name": "{{ $coach->name }} Hire",
            "serviceType": "Coach hire",
            "description": @json($coach->description),
            "provider": { "@id": "{{ url('/') }}#organization" },
            "areaServed": { "@type": "Country", "name": "United Kingdom" }
        }
        </script>
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('home') }}" },
                { "@type": "ListItem", "position": 2, "name": "Our Fleet", "item": "{{ route('fleet.index') }}" },
                { "@type": "ListItem", "position": 3, "name": "{{ $coach->name }}", "item": "{{ route('fleet.show', $coach) }}" }
            ]
        }
        </script>
    </x-slot:head>

    <x-page-header
        eyebrow="Our Fleet"
        :title="$coach->name"
        :subtitle="$coach->description"
    >
        <div class="mt-8 flex flex-wrap gap-4" data-hero-reveal>
            <a href="{{ route('booking.create', ['coach' => $coach->id]) }}" class="btn-primary">Book This Coach</a>
            <a href="{{ route('quote.create', ['coach' => $coach->id]) }}" class="btn-ghost-light">Get a Quote</a>
        </div>
    </x-page-header>

    <section class="py-16 sm:py-24">
        <div class="container-site grid gap-12 lg:grid-cols-[1.3fr_1fr] lg:gap-16">
            <div class="gsap-reveal">
                <div class="relative aspect-[16/10] overflow-hidden rounded-3xl bg-gradient-to-br from-navy-800 to-navy-950">
                    @if ($coach->image)
                        <img
                            src="{{ str_starts_with($coach->image, 'http') ? $coach->image : asset('storage/'.$coach->image) }}"
                            alt="{{ $coach->name }} available for hire from Brit Travels"
                            width="960" height="600"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center p-14 text-navy-300">
                            <x-coach-illustration />
                        </div>
                    @endif
                    <span class="absolute right-5 top-5 rounded-full bg-accent-400 px-4 py-2 text-sm font-bold text-navy-950">{{ $coach->seats }} seats</span>
                </div>

                @if ($coach->gallery)
                    <div class="mt-5 grid grid-cols-3 gap-4">
                        @foreach ($coach->gallery as $image)
                            <img src="{{ asset('storage/'.$image) }}" alt="{{ $coach->name }} interior and exterior" loading="lazy" class="aspect-[4/3] w-full rounded-2xl object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="gsap-reveal" data-reveal="right">
                <h2 class="font-display text-2xl font-semibold text-navy-950">What's included</h2>
                @if ($coach->amenities)
                    <ul class="mt-6 space-y-3">
                        @foreach ($coach->amenities as $amenity)
                            <li class="flex items-center gap-3 text-sm text-navy-700">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </span>
                                {{ $amenity }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-9 rounded-3xl border border-navy-100 bg-navy-50/60 p-7">
                    <h3 class="font-display text-lg font-semibold text-navy-950">Every hire includes</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-navy-600">
                        <li>• Fully licensed, DBS-checked professional driver</li>
                        <li>• Fuel, tolls &amp; standard mileage</li>
                        <li>• Door-to-door pickup and return</li>
                        <li>• Free amendments where possible</li>
                    </ul>
                    <a href="{{ route('booking.create', ['coach' => $coach->id]) }}" class="btn-primary mt-6 w-full">Book the {{ $coach->name }}</a>
                </div>
            </div>
        </div>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-navy-50/60 py-16 sm:py-20">
            <div class="container-site">
                <h2 class="section-title gsap-reveal">Other coaches you might like</h2>
                <div class="mt-10 grid gap-7 sm:grid-cols-2 lg:grid-cols-3 gsap-stagger">
                    @foreach ($others as $other)
                        <x-coach-card :coach="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
