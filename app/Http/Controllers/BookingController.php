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
            'coaches' => Coach::active()->get(),
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create([
            ...$request->safe()->except('website'),
            'reference' => Booking::generateReference(),
            'status' => Booking::STATUS_PENDING,
        ]);

        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        if ($ownerEmail) {
            Mail::to($ownerEmail)->send(new BookingReceivedMail($booking));
        }
        Mail::to($booking->email)->send(new BookingConfirmationMail($booking));

        return redirect()->route('booking.success', $booking);
    }

    public function success(Booking $booking)
    {
        return view('booking.success', ['booking' => $booking]);
    }
}
