<x-layout
    title="About Us — UK Coach Hire Specialists"
    description="Brit Travels is a UK coach hire company offering modern vehicles, professional drivers, and stress-free group travel for schools, businesses, weddings, and tours."
>
    <x-page-header
        eyebrow="About Brit Travels"
        title="Group travel, done properly"
        subtitle="We believe hiring a coach should be as easy as booking a taxi — clear prices, quick answers, and vehicles that turn up on time, every time."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site grid gap-14 lg:grid-cols-2 lg:gap-20">
            <div class="gsap-reveal">
                <p class="section-eyebrow">Our Story</p>
                <h2 class="section-title">Built around one simple idea: make group travel effortless</h2>
                <div class="mt-6 space-y-4 text-[15px] leading-relaxed text-navy-600">
                    <p>Brit Travels was founded to take the stress out of moving groups of people around the UK. Whether it's a school year group heading to a museum, a company shuttling staff between sites, or a wedding party travelling between venues — we handle the logistics so you can focus on the day itself.</p>
                    <p>Our fleet spans everything from executive 8-seater minibuses to full-size 70-seat touring coaches, all maintained to the highest standard and driven by fully licensed, DBS-checked professionals.</p>
                    <p>We keep our pricing transparent, our quotations fast, and our booking process simple. That's why schools, businesses, sports clubs, and event planners across the country trust us again and again.</p>
                </div>
            </div>
            <div class="grid content-start gap-6 sm:grid-cols-2 gsap-stagger">
                @foreach ([
                    ['title' => 'Safety First', 'text' => 'DBS-checked drivers, rigorous vehicle maintenance, and full compliance with UK transport regulations.'],
                    ['title' => 'Always On Time', 'text' => 'We plan every route in advance and build in contingency, so your group is never left waiting.'],
                    ['title' => 'Honest Pricing', 'text' => 'The price we quote is the price you pay. No hidden fees, no surprises on the day.'],
                    ['title' => 'Real People', 'text' => 'Speak to a human whenever you need — before, during, and after your journey.'],
                ] as $value)
                    <div class="rounded-3xl border border-navy-100 bg-white p-7">
                        <h3 class="font-display text-lg font-semibold text-navy-950">{{ $value['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-navy-600">{{ $value['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-navy-950 py-16 sm:py-20">
        <div class="container-site grid grid-cols-2 gap-10 text-center lg:grid-cols-4">
            @foreach ([
                ['value' => 500, 'suffix' => '+', 'label' => 'Journeys completed'],
                ['value' => 8, 'suffix' => '', 'label' => 'Vehicle sizes'],
                ['value' => 98, 'suffix' => '%', 'label' => 'Customer satisfaction'],
                ['value' => 100, 'suffix' => '%', 'label' => 'Licensed drivers'],
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

    <section class="py-16 sm:py-24">
        <div class="container-site text-center gsap-reveal">
            <h2 class="section-title mx-auto max-w-2xl">Let's get your group on the road</h2>
            <div class="mt-9 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.create') }}" class="btn-primary">Book a Coach</a>
                <a href="{{ route('quote.create') }}" class="btn-secondary">Get a Free Quote</a>
            </div>
        </div>
    </section>
</x-layout>
