@props(['testimonial', 'showRating' => true])

<figure {{ $attributes->merge(['class' => 'testimonial-card']) }}>
    <figcaption class="flex items-center gap-3.5">
        <span class="testimonial-avatar">{{ str($testimonial->author)->substr(0, 1) }}</span>
        <span class="min-w-0 flex-1">
            <span class="block truncate font-display text-[15px] font-semibold leading-tight text-navy-950">{{ $testimonial->author }}</span>
            @if ($testimonial->role)
                <span class="mt-1 block truncate text-[13px] leading-tight text-navy-500">{{ $testimonial->role }}</span>
            @endif
        </span>
        @if ($showRating)
            <span class="flex shrink-0 gap-0.5 text-accent-400" role="img" aria-label="{{ $testimonial->rating }} out of 5 stars">
                @for ($i = 0; $i < $testimonial->rating; $i++)
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.077 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @endfor
            </span>
        @endif
    </figcaption>

    <blockquote class="testimonial-quote">
        <p>&ldquo;{{ $testimonial->quote }}&rdquo;</p>
    </blockquote>
</figure>
