<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReceivedMail;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
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
            'trip_type' => 'one_way',
            'pickup_location' => 'London Victoria',
            'dropoff_location' => 'Manchester City Centre',
            'pickup_date' => now()->addDays(7)->toDateString(),
            'pickup_time' => '09:30',
            'passengers' => 30,
            'luggage_small' => 4,
            'bags_medium' => 2,
            'luggage_large' => 6,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+447123456789',
        ], $overrides);
    }

    public function test_booking_page_loads(): void
    {
        $this->get(route('booking.create'))->assertOk();
    }

    public function test_one_way_booking_is_stored_and_emails_sent(): void
    {
        Mail::fake();

        $response = $this->post(route('booking.store'), $this->validPayload());

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertSame('pending', $booking->status);
        $this->assertStringStartsWith('BT-', $booking->reference);
        $response->assertRedirect(route('booking.success', $booking));

        Mail::assertQueued(BookingReceivedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
        Mail::assertQueued(BookingConfirmationMail::class, fn ($mail) => $mail->hasTo('jane@example.com'));
    }

    public function test_via_routes_are_stored_in_order(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload([
            'via_routes' => ['Birmingham New Street', 'Stoke-on-Trent'],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(
            ['Birmingham New Street', 'Stoke-on-Trent'],
            Booking::firstOrFail()->via_routes,
        );
    }

    public function test_blank_via_rows_are_stripped_and_do_not_fail_validation(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload([
            'via_routes' => ['  Coventry  ', '', '   '],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(['Coventry'], Booking::firstOrFail()->via_routes);
    }

    public function test_via_routes_are_capped(): void
    {
        $this->post(route('booking.store'), $this->validPayload([
            'via_routes' => array_map(fn ($i) => "Stop {$i}", range(1, 11)),
        ]))->assertSessionHasErrors('via_routes');

        $this->assertSame(0, Booking::count());
    }

    public function test_booking_without_via_routes_is_valid(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload())->assertSessionHasNoErrors();

        $this->assertNull(Booking::firstOrFail()->via_routes);
    }

    public function test_notes_field_is_no_longer_offered_on_the_form(): void
    {
        $this->get(route('booking.create'))
            ->assertOk()
            ->assertDontSee('name="notes"', false)
            ->assertSee('name="via_routes[]"', false)
            ->assertSee('Add destination');
    }

    public function test_luggage_counts_are_stored_and_summarised(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload());

        $booking = Booking::firstOrFail();
        $this->assertSame(4, $booking->luggage_small);
        $this->assertSame(2, $booking->bags_medium);
        $this->assertSame(6, $booking->luggage_large);
        $this->assertSame('4 small · 2 medium · 6 large', $booking->luggageSummary());
    }

    public function test_luggage_counts_default_to_zero_when_left_blank(): void
    {
        Mail::fake();

        $payload = $this->validPayload();
        unset($payload['luggage_small'], $payload['bags_medium']);
        $payload['luggage_large'] = '';

        $this->post(route('booking.store'), $payload)->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();
        $this->assertSame(0, $booking->luggage_small);
        $this->assertSame(0, $booking->bags_medium);
        $this->assertSame(0, $booking->luggage_large);
        $this->assertNull($booking->luggageSummary());
    }

    public function test_passengers_is_still_required(): void
    {
        $payload = $this->validPayload();
        unset($payload['passengers']);

        $this->post(route('booking.store'), $payload)->assertSessionHasErrors('passengers');
        $this->assertSame(0, Booking::count());
    }

    public function test_booking_form_puts_passengers_and_luggage_in_step_two(): void
    {
        $html = $this->get(route('booking.create'))->assertOk()->getContent();

        // Step 2 opens at the full-name field, so every one of these must appear after it.
        $stepTwoStart = strpos($html, 'name="name"');
        foreach (['name="passengers"', 'name="bags_medium"', 'name="luggage_small"', 'name="luggage_large"'] as $field) {
            $this->assertGreaterThan($stepTwoStart, strpos($html, $field), "{$field} should render in step 2");
        }

        $this->assertStringContainsString('placeholder="Enter Your Full Name"', $html);
        $this->assertStringContainsString('data-phone-intl', $html);
    }

    public function test_round_trip_requires_return_date_and_time(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload(['trip_type' => 'round_trip']))
            ->assertSessionHasErrors(['return_date', 'return_time']);

        $this->assertSame(0, Booking::count());
        Mail::assertNothingOutgoing();
    }

    public function test_round_trip_booking_is_stored(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload([
            'trip_type' => 'round_trip',
            'return_date' => now()->addDays(9)->toDateString(),
            'return_time' => '18:00',
        ]));

        $this->assertSame(1, Booking::count());
        $this->assertTrue(Booking::first()->isRoundTrip());
    }

    public function test_pickup_date_cannot_be_in_the_past(): void
    {
        $this->post(route('booking.store'), $this->validPayload([
            'pickup_date' => now()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('pickup_date');

        $this->assertSame(0, Booking::count());
    }

    public function test_return_date_cannot_be_before_pickup_date(): void
    {
        $this->post(route('booking.store'), $this->validPayload([
            'trip_type' => 'round_trip',
            'return_date' => now()->addDays(2)->toDateString(),
            'return_time' => '18:00',
            'pickup_date' => now()->addDays(7)->toDateString(),
        ]))->assertSessionHasErrors('return_date');
    }

    public function test_honeypot_blocks_spam(): void
    {
        Mail::fake();

        $this->post(route('booking.store'), $this->validPayload(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Booking::count());
    }

    public function test_success_page_shows_reference(): void
    {
        $booking = Booking::create([
            ...$this->validPayload(),
            'reference' => 'BT-2026-TEST1',
            'status' => Booking::STATUS_PENDING,
        ]);

        $this->get(route('booking.success', $booking))
            ->assertOk()
            ->assertSee('BT-2026-TEST1');
    }
}
