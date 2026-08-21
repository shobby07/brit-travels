<?php

namespace App\Http\Controllers;

use App\Models\Coach;

class FleetController extends Controller
{
    public function index()
    {
        return view('fleet.index', [
            'coaches' => Coach::active(),
        ]);
    }

    public function show(Coach $coach)
    {
        return view('fleet.show', [
            'coach' => $coach,
            'others' => Coach::active()->reject(fn (Coach $other) => $other->slug === $coach->slug)->take(3),
        ]);
    }
}
