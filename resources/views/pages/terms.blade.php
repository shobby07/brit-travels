<x-layout
    title="Terms of Engagement"
    description="Brit Travels terms of engagement covering bookings, payments, amendments, cancellations, passenger conduct, and liability for coach hire services."
>
    <x-page-header
        eyebrow="Legal"
        title="Terms of Engagement"
        subtitle="The terms that apply when you book with Brit Travels. Please read them before confirming your booking."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site max-w-3xl">
            <div class="space-y-10 text-[15px] leading-relaxed text-navy-700">
                @foreach ([
                    ['title' => '1. Bookings', 'body' => 'All bookings are subject to availability and are only confirmed once you receive a confirmation email from Brit Travels quoting your booking reference. Submitting a booking request through our website does not constitute a confirmed booking.'],
                    ['title' => '2. Quotations', 'body' => 'Quotations are free and valid for 14 days from the date of issue unless otherwise stated. Prices may vary depending on final trip details, including changes to dates, times, pickup or drop-off locations, and passenger numbers.'],
                    ['title' => '3. Payment', 'body' => 'Payment terms will be set out in your booking confirmation. Unless otherwise agreed, payment must be received in full before the date of travel.'],
                    ['title' => '4. Amendments', 'body' => 'We will always try to accommodate amendments free of charge. Significant changes (such as route, date, or vehicle size) may affect the price. Please notify us of any changes as early as possible, quoting your booking reference.'],
                    ['title' => '5. Cancellations', 'body' => 'Cancellations must be made in writing (email is acceptable). Cancellation charges may apply depending on how close to the travel date the cancellation is made; details will be provided with your booking confirmation.'],
                    ['title' => '6. Passenger Conduct', 'body' => 'The driver is responsible for the safety of the vehicle at all times. Passengers must follow reasonable instructions from the driver. We reserve the right to refuse carriage to any passenger whose behaviour risks the safety or comfort of others.'],
                    ['title' => '7. Luggage', 'body' => 'Luggage must be of reasonable size and quantity for the vehicle booked. Please tell us in advance about bulky items (e.g. sports equipment, instruments, wheelchairs) so we can allocate a suitable vehicle.'],
                    ['title' => '8. Delays', 'body' => 'While we plan every journey carefully, we cannot accept liability for delays caused by events outside our control, including traffic incidents, severe weather, or road closures. We will always communicate promptly and do everything we can to minimise disruption.'],
                    ['title' => '9. Liability', 'body' => 'Nothing in these terms limits our liability where it would be unlawful to do so. Our vehicles are fully insured for hire and reward as required by UK law.'],
                    ['title' => '10. Contact', 'body' => 'Questions about these terms should be sent to '.setting('email', 'info@brittravel.co.uk').' quoting your booking or quotation reference where applicable.'],
                ] as $section)
                    <div class="gsap-reveal">
                        <h2 class="font-display text-xl font-semibold text-navy-950">{{ $section['title'] }}</h2>
                        <p class="mt-3">{{ $section['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
