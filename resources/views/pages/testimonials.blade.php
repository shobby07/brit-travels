<x-layout
    title="Testimonials — What Our Customers Say"
    description="Read reviews from schools, businesses, wedding planners and sports clubs who trust Brit Travels for coach hire across the UK."
>
    <x-page-header
        eyebrow="Testimonials"
        title="Don't just take our word for it"
        subtitle="Schools, businesses, and event organisers across the UK trust Brit Travels to get their groups where they need to be."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 gsap-stagger">
                @forelse ($testimonials as $testimonial)
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
                @empty
                    <p class="text-navy-500">Testimonials coming soon.</p>
                @endforelse
            </div>

            <div class="mt-16 text-center gsap-reveal">
                <h2 class="font-display text-2xl font-semibold text-navy-950">Ready to join them?</h2>
                <div class="mt-6 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('booking.create') }}" class="btn-primary">Book a Coach</a>
                    <a href="{{ route('quote.create') }}" class="btn-secondary">Get a Free Quote</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
