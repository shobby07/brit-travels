<header
    id="site-nav"
    x-data="{ open: false }"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
>
    <nav class="container-site flex items-center justify-between py-4" aria-label="Main navigation">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Brit Travel home">
            <img src="{{ asset('images/logo-mark-dark-bg.png') }}" alt="" class="h-9 w-auto sm:h-10">
            <span class="font-display text-lg font-semibold tracking-tight text-white">Brit<span class="text-accent-400">Travel</span></span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-8 lg:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Home</a>
            <a href="{{ route('fleet.index') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Our Fleet</a>

            @if ($navLocations->isNotEmpty())
                <div
                    class="relative"
                    x-data="{ coachOpen: false }"
                    @mouseenter="coachOpen = true"
                    @mouseleave="coachOpen = false"
                >
                    <button
                        type="button"
                        @click="coachOpen = !coachOpen"
                        class="flex items-center gap-1 text-sm font-medium text-white/80 transition-colors hover:text-white"
                        :aria-expanded="coachOpen"
                        aria-controls="coach-hire-menu"
                    >
                        Coach Hire
                        <svg class="h-4 w-4 transition-transform duration-200" :class="coachOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div
                        id="coach-hire-menu"
                        x-show="coachOpen"
                        x-cloak
                        x-transition:enter="transition duration-200 ease-out"
                        x-transition:enter-start="-translate-y-1 opacity-0"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="transition duration-150 ease-in"
                        x-transition:leave-start="translate-y-0 opacity-100"
                        x-transition:leave-end="-translate-y-1 opacity-0"
                        @click.outside="coachOpen = false"
                        class="absolute left-1/2 top-full z-50 w-64 -translate-x-1/2 pt-3"
                    >
                        <div class="max-h-[70vh] overflow-y-auto rounded-2xl border border-white/10 bg-navy-950/95 p-2 shadow-xl shadow-black/30 backdrop-blur-lg">
                            @foreach ($navLocations as $location)
                                <a href="{{ route('coach-hire.show', $location) }}" class="block rounded-lg px-4 py-2.5 text-sm font-medium text-white/80 transition-colors hover:bg-white/5 hover:text-white">Coach Hire {{ $location->name }}</a>
                            @endforeach
                            <a href="{{ route('coach-hire.index') }}" class="mt-1 flex items-center justify-between rounded-lg border-t border-white/10 px-4 py-2.5 text-sm font-semibold text-accent-400 transition-colors hover:bg-white/5">
                                View all locations
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('coach-hire.index') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Coach Hire</a>
            @endif

            <a href="{{ route('about') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">About Us</a>
            <a href="{{ route('faq') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">FAQ</a>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Contact</a>
        </div>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('quote.create') }}" class="btn-ghost-light !px-5 !py-2.5">Get a Quote</a>
            <a href="{{ route('booking.create') }}" class="btn-primary !px-5 !py-2.5">Book Now</a>
        </div>

        {{-- Mobile hamburger --}}
        <button
            @click="open = !open"
            class="flex h-11 w-11 items-center justify-center rounded-full text-white lg:hidden"
            :aria-expanded="open"
            aria-controls="mobile-menu"
            aria-label="Toggle menu"
        >
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </nav>

    {{-- Mobile menu --}}
    <div
        id="mobile-menu"
        x-show="open"
        x-cloak
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-2 opacity-0"
        class="border-t border-white/10 bg-navy-950/95 backdrop-blur-lg lg:hidden"
    >
        <div class="container-site flex flex-col gap-1 py-4">
            <a href="{{ route('home') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">Home</a>
            <a href="{{ route('fleet.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">Our Fleet</a>

            @if ($navLocations->isNotEmpty())
                <div x-data="{ coachOpen: false }">
                    <button
                        type="button"
                        @click="coachOpen = !coachOpen"
                        class="flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5"
                        :aria-expanded="coachOpen"
                        aria-controls="mobile-coach-hire"
                    >
                        Coach Hire
                        <svg class="h-4 w-4 transition-transform duration-200" :class="coachOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="mobile-coach-hire" x-show="coachOpen" x-cloak class="mt-1 flex flex-col gap-0.5 border-l border-white/10 pl-3">
                        @foreach ($navLocations as $location)
                            <a href="{{ route('coach-hire.show', $location) }}" class="rounded-lg px-4 py-2.5 text-sm text-white/70 hover:bg-white/5">Coach Hire {{ $location->name }}</a>
                        @endforeach
                        <a href="{{ route('coach-hire.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-accent-400 hover:bg-white/5">View all locations</a>
                    </div>
                </div>
            @else
                <a href="{{ route('coach-hire.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">Coach Hire</a>
            @endif

            <a href="{{ route('about') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">About Us</a>
            <a href="{{ route('faq') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">FAQ</a>
            <a href="{{ route('contact') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-white/85 hover:bg-white/5">Contact</a>
            <div class="mt-3 flex flex-col gap-2 border-t border-white/10 pt-4">
                <a href="{{ route('quote.create') }}" class="btn-ghost-light w-full">Get a Quote</a>
                <a href="{{ route('booking.create') }}" class="btn-primary w-full">Book Now</a>
            </div>
        </div>
    </div>
</header>
