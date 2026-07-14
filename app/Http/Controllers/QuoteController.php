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
            'coaches' => Coach::active()->get(),
            'selectedCoach' => request('coach'),
        ]);
    }

    public function store(StoreQuoteRequest $request)
    {
        $quote = Quote::create([
            ...$request->safe()->except('website'),
            'reference' => Quote::generateReference(),
            'status' => Quote::STATUS_NEW,
        ]);

        $ownerEmail = Setting::get('booking_notification_email', Setting::get('email'));
        if ($ownerEmail) {
            Mail::to($ownerEmail)->send(new QuoteReceivedMail($quote));
        }
        Mail::to($quote->email)->send(new QuoteAcknowledgementMail($quote));

        return redirect()->route('quote.success', $quote);
    }

    public function success(Quote $quote)
    {
        return view('quote.success', ['quote' => $quote]);
    }
}
