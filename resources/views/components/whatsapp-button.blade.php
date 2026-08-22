{{-- Floating "chat on WhatsApp" button, bottom-left on every page.
     Rendered once from the layout; the scroll-to-top button holds the
     bottom-right corner, so the two never overlap.

     Hidden entirely when site.whatsapp_number is blank. --}}
@php
    $whatsappNumber = setting('whatsapp_number');
    // wa.me wants digits only, in full international form (no +, no spaces).
    $whatsappDigits = $whatsappNumber ? preg_replace('/\D+/', '', $whatsappNumber) : null;
    $whatsappMessage = 'Hi '.setting('site_name', 'Brit Travel').", I'd like to enquire about coach hire.";
@endphp

@if ($whatsappDigits)
    <a
        href="https://wa.me/{{ $whatsappDigits }}?text={{ rawurlencode($whatsappMessage) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp"
        {{-- Insets match the scroll-to-top button on the opposite corner so the
             pair reads as one row above the footer. --}}
        class="group fixed bottom-5 left-5 z-[60] flex h-13 w-13 items-center justify-center rounded-full bg-whatsapp text-white shadow-[0_8px_24px_-4px_rgba(37,211,102,0.5)] transition duration-300 hover:bg-whatsapp-600 hover:scale-105 focus-visible:scale-105 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-whatsapp motion-reduce:transition-none sm:bottom-6 sm:left-6 sm:h-14 sm:w-14"
    >
        {{-- Soft outward pulse to draw the eye; suppressed for reduced-motion users. --}}
        <span class="absolute inset-0 animate-ping rounded-full bg-whatsapp opacity-40 motion-reduce:hidden" aria-hidden="true"></span>

        <svg class="relative h-6.5 w-6.5 sm:h-7 sm:w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.174.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884a9.82 9.82 0 016.988 2.898 9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>

        {{-- Label slides out on hover; hidden on touch-sized screens where hover doesn't apply. --}}
        <span class="pointer-events-none absolute left-full ml-3 hidden whitespace-nowrap rounded-lg bg-navy-950 px-3 py-2 text-xs font-semibold text-white opacity-0 shadow-lg transition-opacity duration-200 group-hover:opacity-100 group-focus-visible:opacity-100 sm:block motion-reduce:transition-none">
            Chat on WhatsApp
        </span>
    </a>
@endif
