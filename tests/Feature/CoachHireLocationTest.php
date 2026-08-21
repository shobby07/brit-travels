<?php

namespace Tests\Feature;

use App\Models\CoachHireLocation;
use Tests\TestCase;

class CoachHireLocationTest extends TestCase
{
    private function london(): CoachHireLocation
    {
        return CoachHireLocation::findBySlug('london')
            ?? $this->fail('The london location is missing from config/locations.php');
    }

    public function test_index_lists_every_configured_location(): void
    {
        $response = $this->get(route('coach-hire.index'))->assertOk();

        foreach (CoachHireLocation::active() as $location) {
            $response->assertSee(route('coach-hire.show', $location));
        }
    }

    public function test_every_location_page_loads_with_structured_data(): void
    {
        foreach (CoachHireLocation::active() as $location) {
            $this->get(route('coach-hire.show', $location))
                ->assertOk()
                ->assertSee('application/ld+json', false)
                ->assertSee('"@type":"Service"', false)
                ->assertSee('"areaServed"', false)
                ->assertSee('"@type":"FAQPage"', false)
                ->assertSee('rel="canonical"', false);
        }
    }

    public function test_location_page_shows_its_own_content(): void
    {
        $london = $this->london();

        $this->get(route('coach-hire.show', $london))
            ->assertOk()
            ->assertSee($london->intro_heading)
            ->assertSee($london->why_choose_points[0]['title'])
            ->assertSee($london->faqs[0]['question']);
    }

    public function test_unknown_location_returns_404(): void
    {
        $this->get('/coach-hire/no-such-city')->assertNotFound();
    }

    public function test_location_page_prefills_quick_form_with_city(): void
    {
        $this->get(route('coach-hire.show', $this->london()))
            ->assertOk()
            ->assertSee('name="pickup_location"', false)
            ->assertSee('value="London"', false);
    }

    public function test_location_sidebar_shows_only_the_three_field_teaser(): void
    {
        $this->get(route('coach-hire.show', $this->london()))
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
        $london = $this->london();

        $this->get(route('coach-hire.show', $london))
            ->assertOk()
            ->assertSee('type="image/webp"', false)
            ->assertSee('640w', false)          // responsive variant for small screens
            ->assertSee('1600w', false)         // full-size variant
            ->assertSee('sizes="100vw"', false)
            ->assertSee('width="1600" height="700"', false)
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('alt="'.$london->hero_image_alt.'"', false)
            ->assertSee('images/hero/london-tower-bridge', false)   // self-hosted, not hotlinked
            ->assertDontSee('upload.wikimedia.org', false);
    }

    public function test_hero_does_not_paint_a_photo_credit_over_the_image(): void
    {
        // The credit is kept in config for licensing purposes, but the
        // watermark overlay is not rendered on the hero.
        $this->get(route('coach-hire.show', $this->london()))
            ->assertOk()
            ->assertDontSee('via Wikimedia Commons', false)
            ->assertDontSee('Fuzzypiggy', false);
    }

    public function test_locations_appear_in_sitemap(): void
    {
        $response = $this->get(route('sitemap'))->assertOk();

        foreach (CoachHireLocation::active() as $location) {
            $response->assertSee(route('coach-hire.show', $location), false);
        }
    }

    public function test_navbar_dropdown_lists_locations_on_public_pages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('coach-hire.show', $this->london()))
            ->assertSee('View all locations');
    }

    public function test_configured_locations_are_unique_per_city(): void
    {
        $locations = CoachHireLocation::active();

        $this->assertSame(9, $locations->count());

        $london = $this->london();
        $newcastle = CoachHireLocation::findBySlug('newcastle-upon-tyne');

        // Content must be genuinely distinct, not the same paragraph reworded.
        $this->assertStringContainsString('Heathrow', $london->intro_content);
        $this->assertStringContainsString('Quayside', $newcastle->intro_content);
        $this->assertNotSame($london->intro_content, $newcastle->intro_content);

        // Every city needs its own intro, or the pages compete with each other.
        $this->assertSame(
            $locations->count(),
            $locations->pluck('intro_content')->unique()->count(),
        );
    }

    public function test_every_location_has_a_committed_hero_image(): void
    {
        foreach (CoachHireLocation::active() as $location) {
            $this->assertNotEmpty($location->hero_image, "{$location->slug} has no hero image");
            $this->assertFileExists(public_path($location->hero_image));
            $this->assertNotEmpty($location->hero_image_alt, "{$location->slug} has no hero alt text");
        }
    }
}
