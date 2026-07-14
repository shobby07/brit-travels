<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Quotation Request {$this->quote->reference} — {$this->quote->pickup_location} to {$this->quote->dropoff_location}",
            replyTo: [$this->quote->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-received',
        );
    }
}
