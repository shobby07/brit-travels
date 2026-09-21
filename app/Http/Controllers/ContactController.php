<?php

namespace App\Http\Controllers;

use App\Mail\ContactAcknowledgementMail;
use App\Mail\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['prohibited'], // honeypot
        ]);

        unset($data['website']);

        // Same reasoning as BookingController::store() — nothing is stored, so
        // this email is the only record of the message. Letting the failure
        // surface as a 500 (or reporting it while still showing the green
        // "message sent" banner) would lose the enquiry, so mirror the booking
        // and quote forms and tell the visitor to phone instead.
        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        try {
            Mail::to($ownerEmail)->send(new ContactMessageMail($data));
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('contact_failed', true);
        }

        // The sender's copy is a courtesy; a failure here shouldn't lose a
        // message the office has already received.
        try {
            Mail::to($data['email'])->send(new ContactAcknowledgementMail($data));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('contact_sent', true);
    }
}
