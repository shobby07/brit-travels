<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReceivedMail;
use App\Mail\ContactAcknowledgementMail;
use App\Mail\ContactMessageMail;
use App\Mail\QuoteAcknowledgementMail;
use App\Mail\QuoteReceivedMail;
use App\Models\Booking;
use App\Models\Quote;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Guards what the office actually receives.
 *
 * Nothing is stored, so a booking that arrives unreadable is nearly as bad as
 * one that never arrives. These assert the rendered HTML, not just that a mail
 * was queued — a Blade comment sitting on its own line once broke the Markdown
 * table midway through, pushing the customer's name, email and phone out of the
 * table and into raw "| **Name** | Jane Smith |" text in every one of these
 * emails, while every existing test still passed.
 */
class EmailContentTest extends TestCase
{
    private function booking(array $overrides = []): Booking
    {
        return Booking::fromArray(array_merge([
            'reference' => 'BT-2026-ABCDE',
            'trip_type' => 'round_trip',
            'pickup_location' => 'London Victoria',
            'dropoff_location' => 'Manchester City Centre',
            'via_routes' => ['Milton Keynes', 'Birmingham'],
            'pickup_date' => now()->addDays(7)->toDateString(),
            'pickup_time' => '09:30',
            'return_date' => now()->addDays(10)->toDateString(),
            'return_time' => '18:15',
            'passengers' => 42,
            'luggage_small' => 4,
            'bags_medium' => 2,
            'luggage_large' => 6,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+447123456789',
        ], $overrides));
    }

    private function quote(array $overrides = []): Quote
    {
        return Quote::fromArray(array_merge([
            'reference' => 'QT-2026-ABCDE',
            'trip_type' => 'one_way',
            'pickup_location' => 'Colchester Town Centre',
            'dropoff_location' => 'Heathrow Terminal 5',
            'pickup_date' => now()->addDays(14)->toDateString(),
            'pickup_time' => '06:00',
            'passengers' => 16,
            'name' => 'Ade Okafor',
            'email' => 'ade@example.com',
            'phone' => '+447987654321',
        ], $overrides));
    }

    /**
     * Every detail row sits in a real table cell. Asserting on the <td> is what
     * catches a broken table: a plain assertStringContainsString('Jane Smith')
     * passes just as happily when the row has degraded to pipe text.
     */
    private function assertRowsRendered(string $html, array $rows): void
    {
        $this->assertStringNotContainsString(
            '| <strong>',
            $html,
            'The Markdown table broke — rows rendered as raw pipe text instead of table cells. '
            .'Check for a Blade comment or blank line inside emails/partials/trip-details.blade.php.'
        );

        foreach ($rows as $label => $value) {
            $this->assertMatchesRegularExpression(
                '#<td[^>]*><strong[^>]*>'.preg_quote($label, '#').'</strong></td>\s*<td[^>]*>'.preg_quote($value, '#').'</td>#',
                $html,
                "The \"{$label}\" row did not render as a table cell containing \"{$value}\"."
            );
        }
    }

    public function test_office_booking_email_renders_every_detail_in_a_table(): void
    {
        $html = (new BookingReceivedMail($this->booking()))->render();

        $this->assertRowsRendered($html, [
            'Trip type' => 'Round trip',
            'Pickup' => 'London Victoria',
            'Via' => 'Milton Keynes → Birmingham',
            'Drop-off' => 'Manchester City Centre',
            'Passengers' => '42',
            'Luggage' => '4 small · 2 medium · 6 large',
            'Name' => 'Jane Smith',
            'Email' => 'jane@example.com',
            'Phone' => '+447123456789',
        ]);
    }

    public function test_customer_booking_copy_renders_every_detail_in_a_table(): void
    {
        $html = (new BookingConfirmationMail($this->booking()))->render();

        $this->assertRowsRendered($html, [
            'Drop-off' => 'Manchester City Centre',
            'Name' => 'Jane Smith',
            'Phone' => '+447123456789',
        ]);
    }

    /** Quotes omit the via and luggage rows, so the table closes differently. */
    public function test_office_quote_email_renders_every_detail_in_a_table(): void
    {
        $html = (new QuoteReceivedMail($this->quote()))->render();

        $this->assertRowsRendered($html, [
            'Trip type' => 'One way',
            'Pickup' => 'Colchester Town Centre',
            'Drop-off' => 'Heathrow Terminal 5',
            'Passengers' => '16',
            'Name' => 'Ade Okafor',
            'Email' => 'ade@example.com',
            'Phone' => '+447987654321',
        ]);
    }

    /** A one-way booking with no via stops or luggage must not break either. */
    public function test_sparse_booking_email_still_renders_a_table(): void
    {
        $html = (new BookingReceivedMail($this->booking([
            'trip_type' => 'one_way',
            'via_routes' => [],
            'return_date' => null,
            'return_time' => null,
            'luggage_small' => 0,
            'bags_medium' => 0,
            'luggage_large' => 0,
        ])))->render();

        $this->assertRowsRendered($html, [
            'Trip type' => 'One way',
            'Name' => 'Jane Smith',
            'Email' => 'jane@example.com',
        ]);
    }

    /** The subject is what the office sees in the inbox list, so it has to say enough to triage. */
    public function test_office_subjects_identify_the_journey(): void
    {
        $this->assertSame(
            'New Booking Request BT-2026-ABCDE — London Victoria to Manchester City Centre',
            (new BookingReceivedMail($this->booking()))->envelope()->subject,
        );

        $this->assertSame(
            'New Quotation Request QT-2026-ABCDE — Colchester Town Centre to Heathrow Terminal 5',
            (new QuoteReceivedMail($this->quote()))->envelope()->subject,
        );
    }

    /** Replying to the notification must reach the customer, not our own inbox. */
    public function test_office_emails_reply_to_the_customer(): void
    {
        $this->assertSame(
            'jane@example.com',
            (new BookingReceivedMail($this->booking()))->envelope()->replyTo[0]->address,
        );

        $this->assertSame(
            'ade@example.com',
            (new QuoteReceivedMail($this->quote()))->envelope()->replyTo[0]->address,
        );
    }

    private function contact(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '07700 900123',
            'message' => "Do you cover Scotland?\n\nWe need a 50-seater in March.",
        ], $overrides);
    }

    public function test_office_contact_email_renders_every_detail_in_a_table(): void
    {
        $html = (new ContactMessageMail($this->contact()))->render();

        $this->assertRowsRendered($html, [
            'Name' => 'Jane Smith',
            'Email' => 'jane@example.com',
            'Phone' => '07700 900123',
        ]);

        $this->assertStringContainsString('Do you cover Scotland?', $html);
        $this->assertStringContainsString('We need a 50-seater in March.', $html);
    }

    /** Phone is optional on the contact form, so its row drops out. */
    public function test_office_contact_email_renders_without_a_phone_number(): void
    {
        $html = (new ContactMessageMail($this->contact(['phone' => null])))->render();

        $this->assertRowsRendered($html, [
            'Name' => 'Jane Smith',
            'Email' => 'jane@example.com',
        ]);

        $this->assertStringNotContainsString('Phone', $html);
    }

    /**
     * The sender's copy quotes their message back. It is rendered as a plain
     * block rather than a blockquote because a "> " quote is broken by the
     * first blank line, which would leave half a multi-line message unquoted.
     */
    public function test_contact_acknowledgement_echoes_the_message_and_promises_24_hours(): void
    {
        $html = (new ContactAcknowledgementMail($this->contact()))->render();

        $this->assertStringContainsString('within 24 hours', $html);
        $this->assertStringContainsString(Setting::get('phone'), $html);

        // Each line of the message becomes its own paragraph. Matching the tag
        // (rather than just the text) is what proves it was not swallowed into
        // a half-broken blockquote — the inline styles are added by premailer.
        foreach (['Do you cover Scotland?', 'We need a 50-seater in March.'] as $line) {
            $this->assertMatchesRegularExpression(
                '#<p[^>]*>'.preg_quote($line, '#').'</p>#',
                $html,
                "The message line \"{$line}\" did not render as its own paragraph.",
            );
        }
    }

    /** Both auto-replies commit to the same turnaround the site promises. */
    public function test_customer_auto_replies_promise_24_hours(): void
    {
        $this->assertStringContainsString(
            'within 24 hours',
            (new BookingConfirmationMail($this->booking()))->render(),
        );

        $this->assertStringContainsString(
            'within 24 hours',
            (new QuoteAcknowledgementMail($this->quote()))->render(),
        );
    }

    /** Addressed to the sender, and not carrying the office subject line. */
    public function test_contact_acknowledgement_subject_names_the_business(): void
    {
        $this->assertSame(
            'Thanks for getting in touch — Brit Travel',
            (new ContactAcknowledgementMail($this->contact()))->envelope()->subject,
        );
    }

    public function test_mail_test_command_sends_a_sample_booking(): void
    {
        Mail::fake();

        $this->artisan('mail:test', ['email' => 'office@example.com'])
            ->assertSuccessful();

        Mail::assertSent(BookingReceivedMail::class, fn ($mail) => $mail->hasTo('office@example.com'));
    }

    public function test_mail_test_command_defaults_to_the_notification_address(): void
    {
        Mail::fake();
        config(['site.booking_notification_email' => 'owner@example.com']);

        $this->artisan('mail:test')->assertSuccessful();

        Mail::assertSent(BookingReceivedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
    }
}
