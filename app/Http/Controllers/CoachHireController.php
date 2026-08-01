<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\CoachHireLocation;

class CoachHireController extends Controller
{
    public function index()
    {
        return view('coach-hire.index', [
            'locations' => CoachHireLocation::active()->get(),
        ]);
    }

    public function show(CoachHireLocation $location)
    {
        abort_unless($location->is_active, 404);

        return view('coach-hire.show', [
            'location' => $location,
            'coaches' => Coach::active()->get(),
            'others' => CoachHireLocation::active()->whereKeyNot($location->id)->get(),
        ]);
    }
}
