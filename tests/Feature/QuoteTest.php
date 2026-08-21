<?php

namespace Tests\Feature;

use App\Mail\QuoteAcknowledgementMail;
use App\Mail\QuoteReceivedMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['site.booking_notification_email' => 'owner@example.com']);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'pickup_location' => 'Birmingham',
            'dropoff_location' => 'Heathrow Airport',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '07000000000',
            'message' => 'Roughly 40 people, flexible on dates.',
        ], $overrides);
    }

    public function test_quote_page_loads(): void
    {
        $this->get(route('quote.create'))->assertOk();
    }

    public function test_quote_emails_the_office_and_the_customer(): void
    {
        Mail::fake();

        $this->post(route('quote.store'), $this->validPayload())
            ->assertRedirect(route('quote.success'));

        Mail::assertSent(QuoteReceivedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
        Mail::assertSent(QuoteAcknowledgementMail::class, fn ($mail) => $mail->hasTo('john@example.com'));

        $quote = Mail::sent(QuoteReceivedMail::class)->first()->quote;
        $this->assertStringStartsWith('QT-', $quote->reference);
    }

    public function test_required_fields_are_validated(): void
    {
        Mail::fake();

        $this->post(route('quote.store'), [])
            ->assertSessionHasErrors(['pickup_location', 'dropoff_location', 'name', 'email', 'phone']);

        Mail::assertNothingSent();
    }

    public function test_honeypot_blocks_spam(): void
    {
        Mail::fake();

        $this->post(route('quote.store'), $this->validPayload(['website' => 'spam']))
            ->assertSessionHasErrors('website');

        Mail::assertNothingSent();
    }

    public function test_success_page_shows_reference_from_the_session(): void
    {
        $this->withSession(['quote' => [
            'reference' => 'QT-2026-TEST1',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]])
            ->get(route('quote.success'))
            ->assertOk()
            ->assertSee('QT-2026-TEST1');
    }

    public function test_success_page_redirects_when_visited_directly(): void
    {
        $this->get(route('quote.success'))->assertRedirect(route('quote.create'));
    }
}
