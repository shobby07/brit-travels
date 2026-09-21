<?php

namespace App\Console\Commands;

use App\Mail\BookingReceivedMail;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Verifies that booking notifications can actually be delivered.
 *
 * Nothing is stored on this site, so the notification email *is* the booking —
 * if SMTP is misconfigured, enquiries are lost with nothing to recover. This
 * sends a real BookingReceivedMail so a pass proves the whole chain (SMTP
 * credentials, template rendering, subject line, spam placement) rather than
 * just that a socket opened.
 */
class MailTest extends Command
{
    protected $signature = 'mail:test {email? : Where to send it — defaults to the booking notification address}';

    protected $description = 'Send a sample booking notification to check the mail configuration';

    public function handle(): int
    {
        $recipient = $this->argument('email')
            ?? Setting::get('booking_notification_email', Setting::get('email'));

        if (! $recipient) {
            $this->components->error('No recipient. Set BOOKING_NOTIFICATION_EMAIL or pass an address.');

            return self::FAILURE;
        }

        // Print the resolved values rather than the raw environment: on Laravel
        // Cloud config is cached at build time, so an environment variable
        // changed without a redeploy still shows its old value here — which is
        // the single most common reason a "fixed" mail setup keeps failing.
        $this->components->twoColumnDetail('<fg=gray>Setting</>', '<fg=gray>Value in use</>');
        foreach ([
            'Mailer' => config('mail.default'),
            'Host' => config('mail.mailers.smtp.host'),
            'Port' => (string) config('mail.mailers.smtp.port'),
            'Scheme' => config('mail.mailers.smtp.scheme') ?: '(inferred from port)',
            'Username' => config('mail.mailers.smtp.username') ?: '(none)',
            'Password' => config('mail.mailers.smtp.password') ? '(set)' : '<fg=red>(empty)</>',
            'From' => config('mail.from.address'),
            'Sending to' => $recipient,
        ] as $label => $value) {
            $this->components->twoColumnDetail($label, $value);
        }

        $this->newLine();

        if (config('mail.default') === 'log') {
            $this->components->warn('MAIL_MAILER is "log" — this will be written to storage/logs/laravel.log, not sent.');
        }

        try {
            Mail::to($recipient)->send(new BookingReceivedMail($this->sampleBooking()));
        } catch (\Throwable $e) {
            $this->components->error('Send failed: '.$e->getMessage());
            $this->line('  <fg=gray>'.get_class($e).'</>');

            return self::FAILURE;
        }

        $this->components->info("Sent to {$recipient}.");
        $this->line('  <fg=gray>Check the inbox — and the spam folder the first time. If it landed in</>');
        $this->line('  <fg=gray>spam, mark it "not spam" so real bookings reach the inbox.</>');

        return self::SUCCESS;
    }

    /**
     * A filled-in booking, so the test email exercises every row of the
     * details table — including the via stops and luggage rows that a sparse
     * example would skip and leave untested.
     */
    private function sampleBooking(): Booking
    {
        return Booking::fromArray([
            'reference' => 'BT-TEST-'.strtoupper(str()->random(5)),
            'trip_type' => 'round_trip',
            'pickup_location' => 'Colchester Town Centre',
            'dropoff_location' => 'Heathrow Terminal 5',
            'via_routes' => ['Chelmsford', 'Brentwood'],
            'pickup_date' => now()->addWeek()->toDateString(),
            'pickup_time' => '08:30',
            'return_date' => now()->addWeek()->addDays(3)->toDateString(),
            'return_time' => '19:45',
            'passengers' => 42,
            'luggage_small' => 12,
            'bags_medium' => 30,
            'luggage_large' => 8,
            'name' => 'Test Booking (ignore)',
            'email' => Setting::get('email'),
            'phone' => Setting::get('phone'),
            'notes' => 'This is a test sent by "php artisan mail:test" — no action needed.',
        ]);
    }
}
