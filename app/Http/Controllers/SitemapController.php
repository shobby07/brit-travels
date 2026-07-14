<?php

namespace App\Http\Controllers;

use App\Models\Coach;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $staticRoutes = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('fleet.index'), 'priority' => '0.9'],
            ['loc' => route('booking.create'), 'priority' => '0.9'],
            ['loc' => route('quote.create'), 'priority' => '0.9'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('testimonials'), 'priority' => '0.6'],
            ['loc' => route('faq'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.6'],
            ['loc' => route('terms'), 'priority' => '0.3'],
        ];

        $coaches = Coach::active()->get()->map(fn (Coach $coach) => [
            'loc' => route('fleet.show', $coach),
            'priority' => '0.8',
            'lastmod' => $coach->updated_at?->toAtomString(),
        ]);

        $urls = collect($staticRoutes)->concat($coaches);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
