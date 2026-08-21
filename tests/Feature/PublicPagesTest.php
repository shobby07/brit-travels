<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\Coach;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_all_public_pages_load(): void
    {
        foreach (['home', 'fleet.index', 'coach-hire.index', 'booking.create', 'quote.create', 'about', 'testimonials', 'faq', 'contact', 'terms', 'sitemap'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_every_configured_coach_has_a_page_with_structured_data(): void
    {
        $coaches = Coach::active();

        $this->assertNotEmpty($coaches, 'The fleet config should not be empty.');

        foreach ($coaches as $coach) {
            $this->get(route('fleet.show', $coach))
                ->assertOk()
                ->assertSee($coach->name)
                ->assertSee('application/ld+json', false);
        }
    }

    public function test_unknown_coach_returns_404(): void
    {
        $this->get('/fleet/no-such-coach')->assertNotFound();
    }

    public function test_fleet_index_lists_every_coach(): void
    {
        $response = $this->get(route('fleet.index'))->assertOk();

        foreach (Coach::active() as $coach) {
            $response->assertSee($coach->name);
        }
    }

    public function test_contact_form_sends_email(): void
    {
        Mail::fake();
        config(['site.booking_notification_email' => 'owner@example.com']);

        $this->post(route('contact.send'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'message' => 'Do you cover Scotland?',
        ])->assertSessionHas('contact_sent');

        Mail::assertSent(ContactMessageMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }

    public function test_contact_form_rejects_honeypot_submissions(): void
    {
        Mail::fake();

        $this->post(route('contact.send'), [
            'name' => 'Spam Bot',
            'email' => 'spam@example.com',
            'message' => 'Buy things',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        Mail::assertNothingSent();
    }

    public function test_sitemap_includes_every_coach_and_location(): void
    {
        $response = $this->get(route('sitemap'))->assertOk();

        foreach (Coach::active() as $coach) {
            $response->assertSee(route('fleet.show', $coach), false);
        }
    }
}
