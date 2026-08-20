<x-layout
    title="Our Fleet | Coach & Minibus Hire from 8 to 70 Seats"
    description="Explore the Brit Travel fleet: modern coaches and minibuses from 8 to 70 seats, all with professional drivers. Find the right vehicle for your group and book online."
>
    <x-page-header
        eyebrow="Our Fleet"
        title="A coach for every kind of journey"
        subtitle="From executive 8-seater minibuses to 70-seat touring coaches. Every vehicle is modern, air-conditioned, and driven by a licensed professional."
        image="images/hero/fleet-motorway-night.webp"
        imageAlt="Long-exposure light trails from traffic on a motorway at night"
    />

    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3 gsap-stagger">
                @foreach ($coaches as $coach)
                    <x-coach-card :coach="$coach" :eager="$loop->first" />
                @endforeach
            </div>

            <div class="mt-16 rounded-3xl bg-navy-950 p-10 text-center sm:p-14 gsap-reveal">
                <h2 class="font-display text-2xl font-semibold text-white sm:text-3xl">Let’s Plan Your Journey</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm text-white/60">Have a trip in mind? Send us your travel details and group size, and we’ll help you arrange the right coach for your journey.</p>
                <a href="{{ route('quote.create') }}" class="btn-primary mt-7">Get a Free Quote</a>
            </div>
        </div>
    </section>
</x-layout>
