<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\Coach;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_load(): void
    {
        foreach (['home', 'fleet.index', 'booking.create', 'quote.create', 'about', 'testimonials', 'faq', 'contact', 'terms', 'sitemap'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_coach_page_loads_with_structured_data(): void
    {
        $coach = Coach::create([
            'name' => '49 Seater Coach',
            'slug' => '49-seater-coach',
            'seats' => 49,
            'description' => 'A great coach.',
            'is_active' => true,
        ]);

        $this->get(route('fleet.show', $coach))
            ->assertOk()
            ->assertSee('49 Seater Coach')
            ->assertSee('application/ld+json', false);
    }

    public function test_inactive_coach_returns_404(): void
    {
        $coach = Coach::create([
            'name' => 'Hidden Coach',
            'slug' => 'hidden-coach',
            'seats' => 10,
            'is_active' => false,
        ]);

        $this->get(route('fleet.show', $coach))->assertNotFound();
    }

    public function test_contact_form_sends_email(): void
    {
        Mail::fake();
        Setting::set('booking_notification_email', 'owner@example.com');

        $this->post(route('contact.send'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'message' => 'Do you cover Scotland?',
        ])->assertSessionHas('contact_sent');

        Mail::assertQueued(ContactMessageMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }

    public function test_home_page_has_seo_meta(): void
    {
        $this->get(route('home'))
            ->assertSee('<meta name="description"', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('og:title', false);
    }
}
