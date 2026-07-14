<x-layout
    title="FAQ — Coach Hire Questions Answered"
    description="Answers to the most common questions about coach hire with Brit Travels: booking, pricing, drivers, cancellations, accessibility and more."
>
    <x-slot:head>
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                @foreach ($faqs as $faq)
                {
                    "@type": "Question",
                    "name": @json($faq->question),
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": @json($faq->answer)
                    }
                }@if (!$loop->last),@endif
                @endforeach
            ]
        }
        </script>
    </x-slot:head>

    <x-page-header
        eyebrow="FAQ"
        title="Frequently asked questions"
        subtitle="Everything you need to know about booking and travelling with Brit Travels. Can't find your answer? Just get in touch."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site max-w-3xl">
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

            <div class="mt-14 rounded-3xl bg-navy-950 p-10 text-center gsap-reveal">
                <h2 class="font-display text-xl font-semibold text-white">Still have a question?</h2>
                <p class="mt-2 text-sm text-white/60">Our team is happy to help — usually replying within a few hours.</p>
                <a href="{{ route('contact') }}" class="btn-primary mt-6">Contact Us</a>
            </div>
        </div>
    </section>
</x-layout>
