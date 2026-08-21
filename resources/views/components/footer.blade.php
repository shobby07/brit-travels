<footer class="bg-navy-950 text-white">
    <div class="container-site grid gap-12 py-16 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Brit Travel home">
                <img src="{{ asset('images/logo-mark-dark-bg.png') }}" alt="" class="h-9 w-auto">
                <span class="font-display text-lg font-semibold tracking-tight">Brit<span class="text-accent-400">Travel</span></span>
            </a>
            <p class="mt-4 text-sm leading-relaxed text-white/60">
                {{ setting('tagline', 'Experience the ease and convenience of booking a coach with Brit Travel.') }}
            </p>

            {{-- Social links: amber squares with a macOS-dock-style hover pop (Tailwind transform + transition only) --}}
            <div class="mt-5 flex items-center gap-3">
                <a
                    href="https://facebook.com/brittravels"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Facebook"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent-400 text-white shadow-sm transition duration-150 ease-out hover:-translate-y-1 hover:scale-110 hover:bg-accent-500 focus-visible:-translate-y-1 focus-visible:scale-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-400 motion-reduce:transition-none"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                    </svg>
                </a>
                <a
                    href="https://instagram.com/brittravels"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Instagram"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent-400 text-white shadow-sm transition duration-150 ease-out hover:-translate-y-1 hover:scale-110 hover:bg-accent-500 focus-visible:-translate-y-1 focus-visible:scale-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-400 motion-reduce:transition-none"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5"/>
                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
            </div>
        </div>

        <nav aria-label="Footer quick links">
            <h2 class="font-display text-sm font-semibold uppercase tracking-wider text-white">Quick Links</h2>
            <ul class="mt-4 space-y-3 text-sm">
                <li><a href="{{ route('booking.create') }}" class="text-white/70 transition-colors hover:text-accent-400">Book a Coach</a></li>
                <li><a href="{{ route('quote.create') }}" class="text-white/70 transition-colors hover:text-accent-400">Get a Quote</a></li>
                <li><a href="{{ route('fleet.index') }}" class="text-white/70 transition-colors hover:text-accent-400">Our Fleet</a></li>
                <li><a href="{{ route('about') }}" class="text-white/70 transition-colors hover:text-accent-400">About Us</a></li>
                <li><a href="{{ route('testimonials') }}" class="text-white/70 transition-colors hover:text-accent-400">Testimonials</a></li>
                <li><a href="{{ route('terms') }}" class="text-white/70 transition-colors hover:text-accent-400">Terms of Engagement</a></li>
            </ul>
        </nav>

        <nav aria-label="Footer fleet links">
            <h2 class="font-display text-sm font-semibold uppercase tracking-wider text-white">Popular Coaches</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach (\App\Models\Coach::active()->take(5) as $coach)
                    <li><a href="{{ route('fleet.show', $coach) }}" class="text-white/70 transition-colors hover:text-accent-400">{{ $coach->name }} Hire</a></li>
                @endforeach
            </ul>
        </nav>

        <div>
            <h2 class="font-display text-sm font-semibold uppercase tracking-wider text-white">Company Information</h2>
            <ul class="mt-4 space-y-3 text-sm text-white/70">
                @if (setting('phone'))
                    <li>
                        <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="flex items-center gap-2 transition-colors hover:text-accent-400">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            {{ setting('phone') }}
                        </a>
                    </li>
                @endif
                <li>
                    <a href="mailto:{{ setting('email') }}" class="flex items-center gap-2 transition-colors hover:text-accent-400">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        {{ setting('email') }}
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    {{ setting('address', 'United Kingdom') }}
                </li>
            </ul>
        </div>
    </div>

    <div class="container-site">
        {{-- Divider sits inside the container so it lines up with the columns above
             rather than bleeding to the viewport edges. Extra right padding keeps
             the text clear of the floating scroll-to-top button. --}}
        <div class="flex flex-col items-center justify-between gap-3 border-t border-white/10 py-6 pr-6 text-xs text-white/40 sm:flex-row sm:pr-20">
            <p>&copy; {{ now()->year }} {{ setting('site_name', 'Brit Travel') }}. All rights reserved.</p>
            <p>Coach hire &amp; minibus hire across the United Kingdom.</p>
        </div>
    </div>
</footer>
