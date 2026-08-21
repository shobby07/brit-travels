<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\CoachHireLocation;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $staticRoutes = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('fleet.index'), 'priority' => '0.9'],
            ['loc' => route('coach-hire.index'), 'priority' => '0.9'],
            ['loc' => route('booking.create'), 'priority' => '0.9'],
            ['loc' => route('quote.create'), 'priority' => '0.9'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('testimonials'), 'priority' => '0.6'],
            ['loc' => route('faq'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.6'],
            ['loc' => route('terms'), 'priority' => '0.3'],
        ];

        $coaches = Coach::active()->map(fn (Coach $coach) => [
            'loc' => route('fleet.show', $coach),
            'priority' => '0.8',
        ]);

        $locations = CoachHireLocation::active()->map(fn (CoachHireLocation $location) => [
            'loc' => route('coach-hire.show', $location),
            'priority' => '0.8',
        ]);

        $urls = collect($staticRoutes)->concat($coaches)->concat($locations);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
