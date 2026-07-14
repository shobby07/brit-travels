@props(['eyebrow' => null, 'title', 'subtitle' => null])

<section class="relative overflow-hidden bg-navy-950 pb-20 pt-36 sm:pb-24 sm:pt-44">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -right-40 -top-40 h-96 w-96 rounded-full bg-accent-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-navy-500/20 blur-3xl"></div>
    </div>
    <div class="container-site relative">
        @if ($eyebrow)
            <p class="section-eyebrow" data-hero-reveal>{{ $eyebrow }}</p>
        @endif
        <h1 class="max-w-3xl font-display text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl" data-hero-reveal>{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/65 sm:text-lg" data-hero-reveal>{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
