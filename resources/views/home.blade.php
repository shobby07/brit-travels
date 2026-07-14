<x-layout
    :title="null"
    description="Premium coach hire and minibus hire across the UK. Modern fleet from 8 to 70 seats, professional drivers, free quotes, and instant online booking with Brit Travels."
>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-navy-950 pb-24 pt-36 sm:pt-44 lg:pb-32">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -right-52 -top-52 h-[34rem] w-[34rem] rounded-full bg-accent-500/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 h-[28rem] w-[28rem] rounded-full bg-navy-500/25 blur-3xl"></div>
            <svg class="absolute inset-0 h-full w-full opacity-[0.04]" aria-hidden="true"><defs><pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse"><path d="M48 0H0v48" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
        </div>

        <div class="container-site relative">
            <div class="max-w-3xl">
                <p class="section-eyebrow" data-hero-reveal>Coach Hire Across the UK</p>
                <h1 class="font-display text-4xl font-semibold leading-[1.08] tracking-tight text-white sm:text-6xl lg:text-7xl" data-hero-reveal>
                    {{ setting('hero_heading', 'Travel Together, Travel Better') }}
                </h1>
                <p class="mt-6 max-w-xl text-base leading-relaxed text-white/65 sm:text-lg" data-hero-reveal>
                    {{ setting('hero_subheading', 'Modern coaches, professional drivers, and effortless booking — group travel across the UK made simple.') }}
                </p>
                <div class="mt-9 flex flex-wrap gap-4" data-hero-reveal>
                    <a href="{{ route('booking.create') }}" class="btn-primary">Book a Coach</a>
                    <a href="{{ route('quote.create') }}" class="btn-ghost-light">Get a Free Quote</a>
                </div>
            </div>

            {{-- Quick booking widget --}}
            <form
                action="{{ route('booking.create') }}"
                method="GET"
                class="mt-14 grid gap-4 rounded-3xl border border-white/10 bg-white/[0.06] p-6 backdrop-blur-lg sm:grid-cols-2 lg:grid-cols-5 lg:items-end"
                data-hero-reveal
                aria-label="Quick booking"
            >
                <div>
                    <label for="hero-trip-type" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/50">Trip type</label>
                    <select id="hero-trip-type" name="trip_type" class="w-full rounded-xl border border-white/15 bg-navy-900/80 px-4 py-3 text-sm text-white focus:border-accent-400 focus:outline-none">
                        <option value="one_way">One way</option>
                        <option value="round_trip">Round trip</option>
                    </select>
                </div>
                <div>
                    <label for="hero-pickup" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/50">Pickup location</label>
                    <input id="hero-pickup" name="pickup_location" type="text" placeholder="e.g. London" class="w-full rounded-xl border border-white/15 bg-navy-900/80 px-4 py-3 text-sm text-white placeholder:text-white/30 focus:border-accent-400 focus:outline-none">
                </div>
                <div>
                    <label for="hero-dropoff" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/50">Drop-off location</label>
                    <input id="hero-dropoff" name="dropoff_location" type="text" placeholder="e.g. Manchester" class="w-full rounded-xl border border-white/15 bg-navy-900/80 px-4 py-3 text-sm text-white placeholder:text-white/30 focus:border-accent-400 focus:outline-none">
                </div>
                <div>
                    <label for="hero-date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-white/50">Pickup date</label>
                    <input id="hero-date" name="pickup_date" type="date" min="{{ now()->toDateString() }}" class="w-full rounded-xl border border-white/15 bg-navy-900/80 px-4 py-3 text-sm text-white [color-scheme:dark] focus:border-accent-400 focus:outline-none">
                </div>
                <button type="submit" class="btn-primary w-full">Continue Booking</button>
            </form>
        </div>
    </section>

    {{-- Why us --}}
    <section class="py-20 sm:py-28">
        <div class="container-site">
            <div class="max-w-2xl gsap-reveal">
                <p class="section-eyebrow">Why Brit Travels</p>
                <h2 class="section-title">Group travel without the group-travel headaches</h2>
            </div>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4 gsap-stagger">
                @foreach ([
                    ['title' => 'Easy Online Booking', 'text' => 'Book in under two minutes — choose one-way or round trip, tell us where and when, and we handle the rest.', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Professional Drivers', 'text' => 'Every journey comes with a fully licensed, DBS-checked driver who knows UK roads inside out.', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['title' => 'Modern Fleet', 'text' => 'From 8-seat executive minibuses to 70-seat touring coaches — air-conditioned, comfortable, and maintained to the highest standard.', 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m12 0a1.5 1.5 0 01-3 0M3.75 6.75h16.5M3.75 6.75v10.5h16.5V6.75M3.75 12h16.5'],
                    ['title' => 'Transparent Pricing', 'text' => 'Free same-day quotations with no hidden extras. Budget-friendly rates for schools, businesses, and private groups.', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ] as $feature)
                    <div class="rounded-3xl border border-navy-100 bg-white p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-navy-900/5">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}"/></svg>
                        </span>
                        <h3 class="mt-5 font-display text-lg font-semibold text-navy-950">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-navy-600">{{ $feature['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fleet preview --}}
    <section class="bg-navy-50/60 py-20 sm:py-28">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6 gsap-reveal">
                <div class="max-w-2xl">
                    <p class="section-eyebrow">Our Fleet</p>
                    <h2 class="section-title">The right coach for every group size</h2>
                </div>
                <a href="{{ route('fleet.index') }}" class="btn-secondary">View Full Fleet</a>
            </div>
            <div class="mt-14 grid gap-7 sm:grid-cols-2 lg:grid-cols-4 gsap-stagger">
                @foreach ($coaches->take(4) as $coach)
                    <x-coach-card :coach="$coach" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stats band --}}
    <section class="bg-navy-950 py-16 sm:py-20">
        <div class="container-site grid grid-cols-2 gap-10 text-center lg:grid-cols-4">
            @foreach ([
                ['value' => 500, 'suffix' => '+', 'label' => 'Journeys completed'],
                ['value' => 8, 'suffix' => '', 'label' => 'Coach sizes, 8–70 seats'],
                ['value' => 98, 'suffix' => '%', 'label' => 'Customer satisfaction'],
                ['value' => 24, 'suffix' => '/7', 'label' => 'Booking & support'],
            ] as $stat)
                <div class="gsap-reveal">
                    <p class="font-display text-4xl font-bold text-accent-400 sm:text-5xl">
                        <span data-counter="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] }}
                    </p>
                    <p class="mt-2 text-sm text-white/55">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="py-20 sm:py-28">
            <div class="container-site">
                <div class="max-w-2xl gsap-reveal">
                    <p class="section-eyebrow">Testimonials</p>
                    <h2 class="section-title">Trusted by schools, businesses &amp; event planners</h2>
                </div>
                <div class="mt-14 grid gap-6 sm:grid-cols-2 gsap-stagger">
                    @foreach ($testimonials as $testimonial)
                        <figure class="rounded-3xl border border-navy-100 bg-white p-8">
                            <div class="flex gap-1 text-accent-400" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                @for ($i = 0; $i < $testimonial->rating; $i++)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.077 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                @endfor
                            </div>
                            <blockquote class="mt-4 text-[15px] leading-relaxed text-navy-700">&ldquo;{{ $testimonial->quote }}&rdquo;</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-100 font-display text-sm font-bold text-navy-700">{{ str($testimonial->author)->substr(0, 1) }}</span>
                                <span>
                                    <span class="block text-sm font-semibold text-navy-950">{{ $testimonial->author }}</span>
                                    @if ($testimonial->role)
                                        <span class="block text-xs text-navy-500">{{ $testimonial->role }}</span>
                                    @endif
                                </span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ teaser --}}
    @if ($faqs->isNotEmpty())
        <section class="bg-navy-50/60 py-20 sm:py-28">
            <div class="container-site grid gap-12 lg:grid-cols-[1fr_1.4fr] lg:gap-20">
                <div class="gsap-reveal">
                    <p class="section-eyebrow">FAQs</p>
                    <h2 class="section-title">Questions? We've got answers</h2>
                    <p class="mt-4 text-sm leading-relaxed text-navy-600">Everything you need to know about booking, pricing, and travelling with Brit Travels.</p>
                    <a href="{{ route('faq') }}" class="btn-secondary mt-7">View All FAQs</a>
                </div>
                <div class="space-y-4 gsap-stagger">
                    @foreach ($faqs as $faq)
                        <details class="group rounded-2xl border border-navy-100 bg-white px-6 py-5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex cursor-pointer items-center justify-between gap-4 font-display text-[15px] font-semibold text-navy-950">
                                {{ $faq->question }}
                                <svg class="h-5 w-5 shrink-0 text-accent-500 transition-transform duration-300 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                            </summary>
                            <p class="mt-3 text-sm leading-relaxed text-navy-600">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA band --}}
    <section class="relative overflow-hidden bg-navy-950 py-20 sm:py-24">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/2 top-1/2 h-96 w-[40rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-accent-500/10 blur-3xl"></div>
        </div>
        <div class="container-site relative text-center gsap-reveal">
            <h2 class="mx-auto max-w-2xl font-display text-3xl font-semibold tracking-tight text-white sm:text-4xl lg:text-5xl">Ready to get your group moving?</h2>
            <p class="mx-auto mt-4 max-w-xl text-white/60">Free, no-obligation quotes — usually the same day. Or book online in under two minutes.</p>
            <div class="mt-9 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.create') }}" class="btn-primary">Book a Coach</a>
                <a href="{{ route('quote.create') }}" class="btn-ghost-light">Get a Free Quote</a>
            </div>
        </div>
    </section>
</x-layout>
