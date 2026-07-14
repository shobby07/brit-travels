<x-layout
    title="Contact Us — Coach Hire Enquiries"
    description="Get in touch with Brit Travels for coach hire enquiries, bookings, and quotations. Call, email, or send us a message — we usually reply within a few hours."
>
    <x-page-header
        eyebrow="Contact"
        title="We'd love to hear from you"
        subtitle="Questions about a booking, a custom itinerary, or anything else — send us a message and we'll get straight back to you."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site grid gap-14 lg:grid-cols-[1fr_1.4fr] lg:gap-20">
            <div class="gsap-reveal">
                <h2 class="font-display text-2xl font-semibold text-navy-950">Contact details</h2>
                <ul class="mt-7 space-y-6">
                    @if (setting('phone'))
                        <li class="flex gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-navy-950">Phone</span>
                                <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="text-sm text-navy-600 hover:text-accent-600">{{ setting('phone') }}</a>
                            </span>
                        </li>
                    @endif
                    <li class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-navy-950">Email</span>
                            <a href="mailto:{{ setting('email') }}" class="text-sm text-navy-600 hover:text-accent-600">{{ setting('email') }}</a>
                        </span>
                    </li>
                    <li class="flex gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-navy-950">Coverage</span>
                            <span class="text-sm text-navy-600">{{ setting('address', 'United Kingdom') }} — nationwide coach hire</span>
                        </span>
                    </li>
                </ul>

                <div class="mt-10 rounded-3xl border border-navy-100 bg-navy-50/60 p-7">
                    <h3 class="font-display text-base font-semibold text-navy-950">Need a price?</h3>
                    <p class="mt-2 text-sm text-navy-600">For quotations, use our dedicated quote form — it captures your trip details so we can price it accurately, fast.</p>
                    <a href="{{ route('quote.create') }}" class="btn-primary mt-5 !px-5 !py-2.5 text-xs">Get a Free Quote</a>
                </div>
            </div>

            <div class="gsap-reveal" data-reveal="right">
                @if (session('contact_sent'))
                    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-6 py-5 text-sm font-medium text-green-800" role="status">
                        Thanks — your message has been sent! We'll get back to you shortly.
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="rounded-3xl border border-navy-100 bg-white p-8 sm:p-10">
                    @csrf
                    <h2 class="font-display text-2xl font-semibold text-navy-950">Send us a message</h2>
                    <div class="mt-7 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="contact-name" class="field-label">Your name *</label>
                            <input id="contact-name" name="name" type="text" value="{{ old('name') }}" required class="field-input">
                            @error('name')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact-email" class="field-label">Email address *</label>
                            <input id="contact-email" name="email" type="email" value="{{ old('email') }}" required class="field-input">
                            @error('email')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="contact-phone" class="field-label">Phone number</label>
                            <input id="contact-phone" name="phone" type="tel" value="{{ old('phone') }}" class="field-input">
                            @error('phone')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="contact-message" class="field-label">Message *</label>
                            <textarea id="contact-message" name="message" rows="5" required class="field-input">{{ old('message') }}</textarea>
                            @error('message')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                    <button type="submit" class="btn-primary mt-7 w-full sm:w-auto">Send Message</button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
