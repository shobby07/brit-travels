<x-layout
    title="Coach Hire by Location — City Coach & Minibus Hire Across the UK"
    description="Local coach and minibus hire with professional drivers across the UK. Choose your city for airport transfers, event travel, weddings, day trips and corporate coach hire."
>
    <x-page-header
        eyebrow="Coach Hire by Location"
        title="Local coach hire, wherever your group starts"
        subtitle="From airport transfers and stadium days to weddings and scenic tours — pick your city to see how Brit Travels covers coach and minibus hire in your area."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site">
            @if ($locations->isNotEmpty())
                <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3 gsap-stagger">
                    @foreach ($locations as $location)
                        <a
                            href="{{ route('coach-hire.show', $location) }}"
                            class="group flex flex-col justify-between rounded-3xl border border-navy-100 bg-white p-8 transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-xl hover:shadow-navy-900/10"
                        >
                            <div>
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-50 text-accent-600 transition-colors group-hover:bg-accent-50">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                </span>
                                <h2 class="mt-5 font-display text-xl font-semibold text-navy-950">Coach Hire {{ $location->name }}</h2>
                                @if ($location->intro_heading)
                                    <p class="mt-2 text-sm leading-relaxed text-navy-600">{{ $location->intro_heading }}</p>
                                @endif
                            </div>
                            <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-accent-600">
                                View {{ $location->name }} coach hire
                                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-center text-navy-500">We're adding more locations soon. In the meantime, get in touch and we'll cover your journey anywhere in the UK.</p>
            @endif

            <div class="mt-16 rounded-3xl bg-navy-950 p-10 text-center sm:p-14 gsap-reveal">
                <h2 class="font-display text-2xl font-semibold text-white sm:text-3xl">Don't see your town?</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm text-white/60">We cover coach and minibus hire right across the UK — not just the cities listed here. Tell us where you're travelling and we'll send a free quote.</p>
                <div class="mt-7 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('quote.create') }}" class="btn-primary">Get a Free Quote</a>
                    <a href="{{ route('booking.create') }}" class="btn-ghost-light">Book a Coach</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
