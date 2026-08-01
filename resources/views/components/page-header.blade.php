@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'image' => null,
    'imageAlt' => null,
    'imageCredit' => null,
])

@php
    use Illuminate\Support\Str;

    // Build responsive sources for a locally-stored hero. Companion width
    // variants (…-1024, …-640) are used when present so phones don't download
    // the full 1600px image; a single-file admin upload still works (the
    // existence checks simply skip the missing variants — no broken requests).
    $webpSrcset = [];
    $jpgSrcset = [];
    $heroFallback = null;

    if ($image && ! str_starts_with($image, 'http')) {
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $baseNoExt = $ext ? Str::beforeLast($image, '.'.$ext) : $image;

        foreach ([640, 1024, 1600] as $w) {
            $suffix = $w === 1600 ? '' : "-{$w}";
            $webpRel = $baseNoExt.$suffix.'.webp';
            $jpgRel = $baseNoExt.$suffix.'.jpg';
            if (is_file(public_path('storage/'.$webpRel))) {
                $webpSrcset[] = asset('storage/'.$webpRel)." {$w}w";
            }
            if (is_file(public_path('storage/'.$jpgRel))) {
                $jpgSrcset[] = asset('storage/'.$jpgRel)." {$w}w";
            }
        }

        if (is_file(public_path('storage/'.$baseNoExt.'.jpg'))) {
            $heroFallback = asset('storage/'.$baseNoExt.'.jpg');
        } elseif (is_file(public_path('storage/'.$baseNoExt.'.webp'))) {
            $heroFallback = asset('storage/'.$baseNoExt.'.webp');
        } else {
            $heroFallback = asset('storage/'.$image);
        }
    } elseif ($image) {
        $heroFallback = $image;
    }
@endphp

<section class="relative overflow-hidden bg-navy-950 pb-20 pt-36 sm:pb-24 sm:pt-44">
    @if ($image)
        {{-- Photographic hero: eager-loaded (it's the LCP element, above the fold) with fixed dimensions to avoid layout shift --}}
        <div class="absolute inset-0">
            <picture>
                @if ($webpSrcset)
                    <source type="image/webp" srcset="{{ implode(', ', $webpSrcset) }}" sizes="100vw">
                @endif
                <img
                    src="{{ $heroFallback }}"
                    @if ($jpgSrcset) srcset="{{ implode(', ', $jpgSrcset) }}" sizes="100vw" @endif
                    alt="{{ $imageAlt }}"
                    width="1600" height="700"
                    loading="eager"
                    fetchpriority="high"
                    class="h-full w-full object-cover"
                >
            </picture>
            {{-- Contrast overlay so headline text stays readable over the photo --}}
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/85 to-navy-950/50" aria-hidden="true"></div>
            <div class="absolute inset-0 bg-navy-950/30" aria-hidden="true"></div>
            @if ($imageCredit)
                <p class="absolute bottom-2 right-3 z-10 text-[10px] leading-tight text-white/45 [&_a]:underline hover:[&_a]:text-white/70">{!! $imageCredit !!}</p>
            @endif
        </div>
    @else
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -right-40 -top-40 h-96 w-96 rounded-full bg-accent-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-navy-500/20 blur-3xl"></div>
        </div>
    @endif
    <div class="container-site relative">
        @if ($eyebrow)
            <p class="section-eyebrow" data-hero-reveal>{{ $eyebrow }}</p>
        @endif
        <h1 @class(['max-w-3xl font-display text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl', '[text-shadow:0_2px_18px_rgb(10_21_38_/_0.55)]' => $image]) data-hero-reveal>{{ $title }}</h1>
        @if ($subtitle)
            <p @class(['mt-5 max-w-2xl text-base leading-relaxed sm:text-lg', 'text-white/80 [text-shadow:0_1px_12px_rgb(10_21_38_/_0.5)]' => $image, 'text-white/65' => ! $image]) data-hero-reveal>{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
