<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Booking Request {$this->booking->reference} — {$this->booking->pickup_location} to {$this->booking->dropoff_location}",
            replyTo: [$this->booking->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-received',
        );
    }
}
