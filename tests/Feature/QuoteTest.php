<?php

namespace Tests\Feature;

use App\Mail\QuoteAcknowledgementMail;
use App\Mail\QuoteReceivedMail;
use App\Models\Quote;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('booking_notification_email', 'owner@example.com');
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

    public function test_quote_is_stored_and_emails_sent(): void
    {
        Mail::fake();

        $response = $this->post(route('quote.store'), $this->validPayload());

        $quote = Quote::first();
        $this->assertNotNull($quote);
        $this->assertSame('new', $quote->status);
        $this->assertStringStartsWith('QT-', $quote->reference);
        $response->assertRedirect(route('quote.success', $quote));

        Mail::assertQueued(QuoteReceivedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
        Mail::assertQueued(QuoteAcknowledgementMail::class, fn ($mail) => $mail->hasTo('john@example.com'));
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('quote.store'), [])
            ->assertSessionHasErrors(['pickup_location', 'dropoff_location', 'name', 'email', 'phone']);

        $this->assertSame(0, Quote::count());
    }

    public function test_honeypot_blocks_spam(): void
    {
        Mail::fake();

        $this->post(route('quote.store'), $this->validPayload(['website' => 'spam']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Quote::count());
    }
}
