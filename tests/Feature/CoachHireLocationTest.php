<?php

namespace Tests\Feature;

use App\Models\CoachHireLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachHireLocationTest extends TestCase
{
    use RefreshDatabase;

    private function makeLocation(array $overrides = []): CoachHireLocation
    {
        return CoachHireLocation::create(array_merge([
            'name' => 'Bath',
            'slug' => 'bath',
            'meta_title' => 'Coach Hire Bath | Brit Travel',
            'meta_description' => 'Coach hire in Bath with professional drivers.',
            'intro_heading' => 'Coach Hire in Bath',
            'intro_content' => "First paragraph about Bath.\n\nSecond paragraph about the Roman Baths.",
            'why_choose_points' => [
                ['title' => 'Local drivers', 'description' => 'Drivers who know Bath.'],
            ],
            'faqs' => [
                ['question' => 'Do you cover Bath Spa?', 'answer' => 'Yes, we do.'],
            ],
            'is_active' => true,
        ], $overrides));
    }

    public function test_index_lists_only_active_locations(): void
    {
        $active = $this->makeLocation();
        $this->makeLocation(['name' => 'Hidden City', 'slug' => 'hidden-city', 'is_active' => false]);

        $this->get(route('coach-hire.index'))
            ->assertOk()
            ->assertSee('Coach Hire Bath')
            ->assertSee(route('coach-hire.show', $active))
            ->assertDontSee('Hidden City');
    }

    public function test_location_page_loads_with_unique_content_and_structured_data(): void
    {
        $location = $this->makeLocation();

        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            ->assertSee('Coach Hire in Bath')
            ->assertSee('Second paragraph about the Roman Baths')
            ->assertSee('Local drivers')
            ->assertSee('Do you cover Bath Spa?')
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"Service"', false)
            ->assertSee('"areaServed"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('rel="canonical"', false);
    }

    public function test_location_page_prefills_quick_form_with_city(): void
    {
        $location = $this->makeLocation();

        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            ->assertSee('name="pickup_location"', false)
            ->assertSee('value="Bath"', false);
    }

    public function test_location_sidebar_shows_only_the_three_field_teaser(): void
    {
        $location = $this->makeLocation();

        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            // Hands off to /book by GET rather than posting a booking itself.
            ->assertSee('action="'.route('booking.create').'" method="GET"', false)
            ->assertSee('name="dropoff_location"', false)
            ->assertSee('name="pickup_date"', false)
            // The long-form fields now live on /book only.
            ->assertDontSee('name="passengers"', false)
            ->assertDontSee('name="via_routes[]"', false);
    }

    public function test_quick_form_values_carry_through_to_the_booking_form(): void
    {
        $this->get(route('booking.create', [
            'pickup_location' => 'Bath',
            'dropoff_location' => 'Bristol Temple Meads',
            'pickup_date' => now()->addDays(5)->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('value="Bath"', false)
            ->assertSee('value="Bristol Temple Meads"', false)
            ->assertSee('value="'.now()->addDays(5)->toDateString().'"', false);
    }

    public function test_hero_image_renders_responsive_webp_with_dimensions_and_alt(): void
    {
        // Uses a real self-hosted image (committed under public/images/hero) so
        // the responsive srcset is built from its width variants.
        $location = $this->makeLocation([
            'hero_image' => 'images/hero/london-tower-bridge.webp',
            'hero_image_alt' => 'Coach hire in central London near Tower Bridge',
        ]);

        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            ->assertSee('type="image/webp"', false)
            ->assertSee('640w', false)          // responsive variant for small screens
            ->assertSee('1600w', false)         // full-size variant
            ->assertSee('sizes="100vw"', false)
            ->assertSee('width="1600" height="700"', false)
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('alt="Coach hire in central London near Tower Bridge"', false)
            ->assertSee('images/hero/london-tower-bridge', false)   // self-hosted, not hotlinked
            ->assertDontSee('upload.wikimedia.org', false);
    }

    public function test_hero_does_not_paint_a_photo_credit_over_the_image(): void
    {
        $location = $this->makeLocation([
            'hero_image' => 'images/hero/london-tower-bridge.webp',
            'hero_image_credit' => 'Photo &copy; Fuzzypiggy &middot; CC BY-SA 3.0 via Wikimedia Commons',
        ]);

        // The credit is kept on the record for licensing purposes, but the
        // watermark overlay is gone from the rendered hero.
        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            ->assertDontSee('via Wikimedia Commons', false)
            ->assertDontSee('Fuzzypiggy', false);
    }

    public function test_page_falls_back_gracefully_without_a_hero_image(): void
    {
        $location = $this->makeLocation(['hero_image' => null]);

        // No <img> in the hero, but the page still renders with its heading.
        $this->get(route('coach-hire.show', $location))
            ->assertOk()
            ->assertSee('Coach Hire in Bath')
            ->assertDontSee('fetchpriority="high"', false);
    }

    public function test_inactive_location_returns_404(): void
    {
        $location = $this->makeLocation(['is_active' => false]);

        $this->get(route('coach-hire.show', $location))->assertNotFound();
    }

    public function test_active_locations_appear_in_sitemap(): void
    {
        $active = $this->makeLocation();
        $this->makeLocation(['name' => 'Hidden City', 'slug' => 'hidden-city', 'is_active' => false]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('coach-hire.show', $active))
            ->assertDontSee(route('coach-hire.show', 'hidden-city'));
    }

    public function test_navbar_dropdown_lists_locations_on_public_pages(): void
    {
        $location = $this->makeLocation();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('coach-hire.show', $location))
            ->assertSee('View all locations');
    }

    public function test_seeded_locations_are_unique_per_city(): void
    {
        $this->seed(\Database\Seeders\CoachHireLocationSeeder::class);

        $london = CoachHireLocation::where('slug', 'london')->firstOrFail();
        $newcastle = CoachHireLocation::where('slug', 'newcastle-upon-tyne')->firstOrFail();

        // Nine cities requested in the brief.
        $this->assertSame(9, CoachHireLocation::count());

        // Content must be genuinely distinct, not the same paragraph reworded.
        $this->assertStringContainsString('Heathrow', $london->intro_content);
        $this->assertStringContainsString('Quayside', $newcastle->intro_content);
        $this->assertNotSame($london->intro_content, $newcastle->intro_content);
    }

    public function test_seeder_wires_up_hero_images_with_attribution(): void
    {
        $this->seed(\Database\Seeders\CoachHireLocationSeeder::class);

        $london = CoachHireLocation::where('slug', 'london')->firstOrFail();

        // The self-hosted WebP is committed to the repo, so the seeder links it
        // up together with descriptive alt text and a licence credit.
        $this->assertSame('images/hero/london-tower-bridge.webp', $london->hero_image);
        $this->assertNotEmpty($london->hero_image_alt);
        $this->assertStringContainsString('Wikimedia Commons', $london->hero_image_credit);
    }
}
