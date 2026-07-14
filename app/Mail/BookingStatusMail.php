<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $label = Booking::STATUSES[$this->booking->status] ?? ucfirst($this->booking->status);

        return new Envelope(
            subject: "Your booking {$this->booking->reference} is {$label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-status',
        );
    }
}
