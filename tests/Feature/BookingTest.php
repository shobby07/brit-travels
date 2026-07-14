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
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '07123456789',
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
