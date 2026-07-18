@props([
    'items' => ['Book a Coach', 'Travel Together', 'Get a Free Quote'],
])

<div {{ $attributes->merge(['class' => 'marquee border-y border-white/10 bg-navy-950 py-5 sm:py-6']) }} aria-hidden="true">
    <div class="marquee-track">
        @foreach ([0, 1] as $copy)
            <div class="marquee-group">
                @foreach ($items as $item)
                    <span class="font-display text-2xl font-semibold uppercase tracking-[0.18em] text-white sm:text-3xl">{{ $item }}</span>
                    <svg class="h-6 w-6 shrink-0 text-accent-400 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 6-7 12L5 9l7-6zM5 9h14M12 3L9.5 9 12 21M12 3l2.5 6L12 21"/>
                    </svg>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
