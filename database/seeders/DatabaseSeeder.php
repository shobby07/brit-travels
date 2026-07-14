<?php

namespace Database\Seeders;

use App\Models\Coach;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedAdmin();
        $this->seedCoaches();
        $this->seedTestimonials();
        $this->seedFaqs();
        $this->seedSettings();
    }

    private function seedAdmin(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@brittravel.co.uk'],
            [
                'name' => 'Brit Travels Admin',
                'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'ChangeMe!2026')),
            ],
        );
    }

    private function seedCoaches(): void
    {
        $coaches = [
            [
                'seats' => 70,
                'name' => '70 Seater Coach',
                'description' => 'Our largest coach, perfect for big school trips, large corporate events, and full-scale group tours. Travel together comfortably with generous luggage space and modern onboard facilities.',
                'amenities' => ['Air Conditioning', 'Reclining Seats', 'Onboard Toilet', 'PA System', 'Large Luggage Hold', 'USB Charging'],
            ],
            [
                'seats' => 54,
                'name' => '54 Seater Coach',
                'description' => 'A popular full-size touring coach ideal for school outings, sports teams, and long-distance group travel across the UK and beyond.',
                'amenities' => ['Air Conditioning', 'Reclining Seats', 'Onboard Toilet', 'PA System', 'Large Luggage Hold'],
            ],
            [
                'seats' => 49,
                'name' => '49 Seater Coach',
                'description' => 'The workhorse of our fleet — a comfortable executive coach suited to corporate shuttles, weddings, day trips, and airport transfers for large groups.',
                'amenities' => ['Air Conditioning', 'Reclining Seats', 'PA System', 'Large Luggage Hold', 'USB Charging'],
            ],
            [
                'seats' => 33,
                'name' => '33 Seater Midi Coach',
                'description' => 'A versatile midi coach that balances capacity and manoeuvrability — great for medium-sized groups, city tours, and venues with tight access.',
                'amenities' => ['Air Conditioning', 'Reclining Seats', 'Luggage Hold'],
            ],
            [
                'seats' => 24,
                'name' => '24 Seater Mini Coach',
                'description' => 'Comfortable mini coach for smaller groups — ideal for team away days, family gatherings, and airport runs with luggage.',
                'amenities' => ['Air Conditioning', 'Comfortable Seating', 'Luggage Space'],
            ],
            [
                'seats' => 16,
                'name' => '16 Seater Minibus',
                'description' => 'A nimble minibus perfect for small group outings, nights out, sports fixtures, and shuttle services.',
                'amenities' => ['Air Conditioning', 'Comfortable Seating', 'Luggage Space'],
            ],
            [
                'seats' => 12,
                'name' => '12 Seater Minibus',
                'description' => 'Compact and comfortable — ideal for small parties, airport transfers, and local trips where a full coach is more than you need.',
                'amenities' => ['Air Conditioning', 'Comfortable Seating'],
            ],
            [
                'seats' => 8,
                'name' => '8 Seater Minibus',
                'description' => 'Our smallest vehicle — a premium people carrier for intimate groups, executive travel, and door-to-door transfers.',
                'amenities' => ['Air Conditioning', 'Executive Seating', 'Door-to-Door Service'],
            ],
        ];

        foreach ($coaches as $index => $coach) {
            Coach::updateOrCreate(
                ['slug' => str($coach['name'])->slug()],
                [
                    'name' => $coach['name'],
                    'seats' => $coach['seats'],
                    'description' => $coach['description'],
                    'amenities' => $coach['amenities'],
                    'sort_order' => $index,
                    'is_active' => true,
                    'meta_title' => "{$coach['name']} Hire UK | Brit Travels",
                    'meta_description' => "Hire a {$coach['seats']} seater ".($coach['seats'] >= 24 ? 'coach' : 'minibus')." with a professional driver anywhere in the UK. Free instant quote from Brit Travels.",
                ],
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['author' => 'Sarah Mitchell', 'role' => 'School Trip Organiser', 'quote' => 'Brit Travels made our school trip completely stress-free. The coach was spotless, the driver was brilliant with the kids, and everything ran exactly on time.', 'rating' => 5],
            ['author' => 'James Whitfield', 'role' => 'Corporate Events Manager', 'quote' => 'We use Brit Travels for all our company shuttles. Professional drivers, modern coaches, and booking is effortless. Highly recommended.', 'rating' => 5],
            ['author' => 'Priya Sharma', 'role' => 'Wedding Planner', 'quote' => 'They transported 120 wedding guests across three pickups without a single hitch. The quotation was fast and very reasonable.', 'rating' => 5],
            ['author' => 'David Lawson', 'role' => 'Sports Club Coordinator', 'quote' => 'Reliable every single weekend for our away fixtures. The team loves the comfortable seats and USB charging on longer trips.', 'rating' => 5],
        ];

        foreach ($testimonials as $index => $t) {
            Testimonial::updateOrCreate(
                ['author' => $t['author']],
                [...$t, 'sort_order' => $index, 'is_active' => true],
            );
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['question' => 'How do I book a coach with Brit Travels?', 'answer' => 'Simply use our online booking form — choose one-way or round trip, enter your pickup and drop-off locations, date and time, and your contact details. We\'ll confirm your booking by email, usually within a few hours.'],
            ['question' => 'How much does coach hire cost?', 'answer' => 'Prices depend on distance, duration, date, and coach size. Use the "Get a Quote" button and we\'ll send you a free, no-obligation quotation — usually the same day.'],
            ['question' => 'Do your coaches come with a driver?', 'answer' => 'Yes — every hire includes a fully licensed, DBS-checked professional driver. All our drivers are experienced with UK routes and group travel.'],
            ['question' => 'Can I amend or cancel my booking?', 'answer' => 'Yes. Contact us as early as possible and we\'ll amend your booking free of charge where we can. Cancellation terms are set out in our Terms of Engagement.'],
            ['question' => 'What size groups can you accommodate?', 'answer' => 'Our fleet ranges from 8-seater minibuses to 70-seater coaches, so we can cover everything from an executive airport transfer to a full school year group. For larger groups we can supply multiple vehicles.'],
            ['question' => 'Do you provide coaches for airport transfers?', 'answer' => 'Yes — we cover all major UK airports including Heathrow, Gatwick, Manchester, and Birmingham, with meet-and-greet available on request.'],
            ['question' => 'Are your coaches accessible?', 'answer' => 'Several vehicles in our fleet are wheelchair accessible. Please mention accessibility needs in your booking notes and we\'ll make sure the right vehicle is assigned.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                [...$faq, 'sort_order' => $index, 'is_active' => true],
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            'site_name' => 'Brit Travels',
            'tagline' => 'Premium coach hire across the UK',
            'phone' => '+44 0000 000000',
            'email' => 'info@brittravel.co.uk',
            'booking_notification_email' => 'info@brittravel.co.uk',
            'address' => 'United Kingdom',
            'hero_heading' => 'Travel Together, Travel Better',
            'hero_subheading' => 'Modern coaches, professional drivers, and effortless booking — group travel across the UK made simple.',
            'facebook_url' => '',
            'instagram_url' => '',
            'whatsapp_number' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
