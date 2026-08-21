<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Mail\QuoteAcknowledgementMail;
use App\Mail\QuoteReceivedMail;
use App\Models\Coach;
use App\Models\Quote;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function create()
    {
        return view('quote.create', [
            'coaches' => Coach::active(),
            'selectedCoach' => request('coach'),
        ]);
    }

    public function store(StoreQuoteRequest $request)
    {
        $quote = Quote::fromArray($request->safe()->except('website'));

        // See the note in BookingController::store() — the notification email
        // is the only record of this request, so a failure must not be silent.
        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        try {
            Mail::to($ownerEmail)->send(new QuoteReceivedMail($quote));
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('quote_failed', true);
        }

        try {
            Mail::to($quote->email)->send(new QuoteAcknowledgementMail($quote));
        } catch (\Throwable $e) {
            report($e);
        }

        // See the note in BookingController::store().
        return redirect()->route('quote.success')->with('quote', [
            'reference' => $quote->reference,
            'name' => $quote->name,
            'email' => $quote->email,
        ]);
    }

    public function success()
    {
        $quote = session('quote');

        if (! $quote) {
            return redirect()->route('quote.create');
        }

        return view('quote.success', ['quote' => (object) $quote]);
    }
}
