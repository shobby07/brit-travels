<?php

namespace App\Http\Controllers;

use App\Models\CoachHireLocation;

class CoachHireController extends Controller
{
    public function index()
    {
        return view('coach-hire.index', [
            'locations' => CoachHireLocation::active(),
        ]);
    }

    public function show(CoachHireLocation $location)
    {
        // The sidebar now shows a three-field teaser that hands off to /book,
        // so the full coach list is no longer needed here.
        return view('coach-hire.show', [
            'location' => $location,
            'others' => CoachHireLocation::active()->reject(fn (CoachHireLocation $other) => $other->slug === $location->slug),
        ]);
    }
}
