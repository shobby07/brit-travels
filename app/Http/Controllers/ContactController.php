<?php

namespace App\Http\Controllers;

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

        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        if ($ownerEmail) {
            Mail::to($ownerEmail)->send(new ContactMessageMail($data));
        }

        return back()->with('contact_sent', true);
    }
}
