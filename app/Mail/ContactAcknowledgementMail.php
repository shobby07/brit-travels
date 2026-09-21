<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The sender's copy of a contact message.
 *
 * Contact messages carry no reference — unlike bookings and quotes there is
 * nothing to quote back — so this leans on the reply-to threading instead, and
 * echoes the message so the sender has a record of what they actually wrote.
 */
class ContactAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone?: string|null, message: string}  $data
     */
    public function __construct(public array $data)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanks for getting in touch — '.Setting::get('site_name', 'Brit Travel'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-acknowledgement',
        );
    }
}
