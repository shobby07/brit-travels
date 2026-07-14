<header
    id="site-nav"
    x-data="{ open: false }"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
>
    <nav class="container-site flex items-center justify-between py-4" aria-label="Main navigation">
        <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="Brit Travels home">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-accent-400 font-display text-lg font-bold text-navy-950">B</span>
            <span class="font-display text-lg font-semibold tracking-tight text-white">Brit<span class="text-accent-400">Travels</span></span>
        </a>

        {{-- Desktop links --}}
        <div class="hidden items-center gap-8 lg:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Home</a>
            <a href="{{ route('fleet.index') }}" class="text-sm font-medium text-white/80 transition-colors hover:text-white">Our Fleet</a>
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
