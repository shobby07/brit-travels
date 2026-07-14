<x-layout title="Quote Request Received">
    <x-slot:head>
        <meta name="robots" content="noindex">
    </x-slot:head>

    <section class="flex min-h-[70vh] items-center bg-navy-950 py-32">
        <div class="container-site max-w-2xl text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-accent-400" data-hero-reveal>
                <svg class="h-10 w-10 text-navy-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            <h1 class="mt-8 font-display text-3xl font-semibold text-white sm:text-5xl" data-hero-reveal>Quote request received!</h1>
            <p class="mt-5 text-white/65" data-hero-reveal>
                Thanks {{ str($quote->name)->before(' ') }} — our team is pricing up your journey now. We'll email your quotation to <strong class="text-white">{{ $quote->email }}</strong>, usually the same day.
            </p>
            <div class="mt-8 inline-block rounded-2xl border border-white/15 bg-white/5 px-8 py-5" data-hero-reveal>
                <p class="text-xs uppercase tracking-widest text-white/45">Your reference</p>
                <p class="mt-1 font-display text-2xl font-bold text-accent-400">{{ $quote->reference }}</p>
            </div>
            <div class="mt-10 flex flex-wrap justify-center gap-4" data-hero-reveal>
                <a href="{{ route('home') }}" class="btn-primary">Back to Home</a>
                <a href="{{ route('fleet.index') }}" class="btn-ghost-light">Browse Our Fleet</a>
            </div>
        </div>
    </section>
</x-layout>
