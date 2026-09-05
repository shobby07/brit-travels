<x-layout
    title="Testimonials | What Our Customers Say"
    description="Read reviews from schools, businesses, wedding planners and sports clubs who trust Brit Travel for coach hire across the UK."
>
    <x-page-header
        eyebrow="Testimonials"
        title="Don't just take our word for it"
        subtitle="Schools, businesses, and event organisers across the UK trust Brit Travel to get their groups where they need to be."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 gsap-stagger">
                @forelse ($testimonials as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
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
