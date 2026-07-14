<?php

namespace App\Http\Controllers;

use App\Models\Coach;

class FleetController extends Controller
{
    public function index()
    {
        return view('fleet.index', [
            'coaches' => Coach::active()->get(),
        ]);
    }

    public function show(Coach $coach)
    {
        abort_unless($coach->is_active, 404);

        return view('fleet.show', [
            'coach' => $coach,
            'others' => Coach::active()->whereKeyNot($coach->id)->take(3)->get(),
        ]);
    }
}
