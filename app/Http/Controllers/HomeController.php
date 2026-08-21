<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Faq;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'coaches' => Coach::active(),
            'testimonials' => Testimonial::active()->take(4),
            'faqs' => Faq::active()->take(4),
        ]);
    }
}
