<x-layout
    title="Book a Coach Online"
    description="Book a coach or minibus online with Brit Travel in under two minutes. One-way or round trip, 8 to 70 seats, professional driver included."
>
    <x-page-header
        eyebrow="Online Booking"
        title="Book your coach"
        subtitle="Two quick steps: tell us about your trip, then your contact details. We'll confirm availability by email — usually within a few hours."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site max-w-3xl">
            <x-booking-form :coaches="$coaches" />
        </div>
    </section>
</x-layout>
