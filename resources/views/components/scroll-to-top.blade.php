{{-- Floating "scroll to top" button with a scroll-progress ring.
     Rendered once from the layout, so it appears on every page.
     Behaviour + progress tracking live in resources/js/app.js. --}}
<button
    id="scroll-to-top"
    type="button"
    aria-label="Scroll to top"
    class="group fixed bottom-6 right-6 z-[60] flex h-13 w-13 items-center justify-center rounded-full bg-navy-950 text-white opacity-0 translate-y-3 pointer-events-none shadow-lg shadow-navy-900/40 transition-all duration-300 hover:bg-navy-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-500"
>
    {{-- Progress ring: track + amber fill. Rotated so it fills from 12 o'clock. --}}
    <svg class="absolute inset-0 h-full w-full -rotate-90" viewBox="0 0 52 52" fill="none" aria-hidden="true">
        <circle cx="26" cy="26" r="23" stroke="currentColor" stroke-width="2.5" class="text-white/15" />
        <circle
            id="scroll-to-top-ring"
            cx="26" cy="26" r="23"
            stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            class="text-accent-400"
            stroke-dasharray="144.51"
            stroke-dashoffset="144.51"
        />
    </svg>
    {{-- Up arrow --}}
    <svg class="relative h-5 w-5 transition-transform duration-300 group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7" />
    </svg>
</button>
