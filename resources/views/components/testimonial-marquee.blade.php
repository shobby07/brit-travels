@props(['testimonials'])

@php
    $items = collect($testimonials)->values();

    // Repeat the set until a single group out-widths the largest viewport, so the
    // -50% loop never exposes a gap on wide screens.
    $perGroup = max(1, (int) ceil(8 / max($items->count(), 1)));

    $row = collect();
    for ($i = 0; $i < $perGroup; $i++) {
        $row = $row->concat($items);
    }

    // The lower lane starts halfway through the set and drifts the other way, so
    // the two rows never line up as matching pairs.
    $offset = intdiv($items->count(), 2);

    // Cards now size to their own text (see .testimonial-marquee .testimonial-card),
    // which makes each lane roughly twice as wide as it used to be. These durations
    // are longer than the old 70s/86s but the travel is longer still, so the lanes
    // read noticeably faster: ~78px/s and ~63px/s, up from ~54 and ~44.
    $lanes = [
        ['items' => $row, 'reverse' => false, 'duration' => '100s'],
        ['items' => $row->slice($offset)->concat($row->take($offset))->values(), 'reverse' => true, 'duration' => '124s'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'testimonial-marquee']) }}>
    @foreach ($lanes as $index => $lane)
        <div
            class="testimonial-track @if ($lane['reverse']) testimonial-track--reverse @endif @if ($index > 0) mt-5 sm:mt-6 @endif"
            style="--drift: {{ $lane['duration'] }}"
        >
            {{-- Two identical groups: the second one slides in as the first slides out. --}}
            @foreach ([0, 1] as $copy)
                <div class="testimonial-group @if ($copy > 0) testimonial-group--clone @endif" @if ($copy > 0 || $index > 0) aria-hidden="true" @endif>
                    @foreach ($lane['items'] as $testimonial)
                        <x-testimonial-card :testimonial="$testimonial" :show-rating="false" />
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
</div>
