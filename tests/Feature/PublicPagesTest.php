<?php

namespace Tests\Feature;

use App\Mail\ContactAcknowledgementMail;
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
        Mail::assertSent(ContactAcknowledgementMail::class, fn ($mail) => $mail->hasTo('alice@example.com'));
    }

    /**
     * The acknowledgement is a courtesy. The office copy has already been
     * delivered by this point, so failing to send the sender's copy must not
     * turn a received message into an error telling them to phone.
     */
    public function test_contact_acknowledgement_failure_does_not_discard_a_delivered_message(): void
    {
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andReturnUsing(function ($mailable) {
            if ($mailable instanceof ContactAcknowledgementMail) {
                throw new \RuntimeException('Acknowledgement bounced');
            }
        });

        $this->post(route('contact.send'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'message' => 'Do you cover Scotland?',
        ])
            ->assertSessionHas('contact_sent')
            ->assertSessionMissing('contact_failed');
    }

    /**
     * Nothing is stored, so a message we failed to email is a message lost.
     * The visitor has to be told to phone instead of being shown the green
     * "message sent" banner for something that never left the server.
     */
    public function test_contact_form_reports_a_send_failure_instead_of_claiming_success(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('SMTP is down'));

        $this->post(route('contact.send'), [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'message' => 'Do you cover Scotland?',
        ])
            ->assertSessionHas('contact_failed')
            ->assertSessionMissing('contact_sent');
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
