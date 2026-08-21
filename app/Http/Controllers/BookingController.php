<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReceivedMail;
use App\Models\Booking;
use App\Models\Coach;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create()
    {
        return view('booking.create', [
            'coaches' => Coach::active(),
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::fromArray($request->safe()->except('website'));

        // Nothing is stored, so the notification email *is* the booking. If it
        // can't be sent the request would vanish silently — tell the visitor
        // rather than showing a thank-you page for something we never received.
        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        try {
            Mail::to($ownerEmail)->send(new BookingReceivedMail($booking));
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('booking_failed', true);
        }

        // The customer's copy is a courtesy; a failure here shouldn't lose a
        // booking the office has already received.
        try {
            Mail::to($booking->email)->send(new BookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            report($e);
        }

        // Nothing is stored, so the thank-you page is handed the details for
        // this one redirect rather than looking them up by reference.
        return redirect()->route('booking.success')->with('booking', [
            'reference' => $booking->reference,
            'name' => $booking->name,
            'email' => $booking->email,
        ]);
    }

    public function success()
    {
        $booking = session('booking');

        // Direct hits (or a refresh after the flash has gone) have nothing to
        // show, so send them somewhere useful instead of a blank page.
        if (! $booking) {
            return redirect()->route('booking.create');
        }

        return view('booking.success', ['booking' => (object) $booking]);
    }
}
