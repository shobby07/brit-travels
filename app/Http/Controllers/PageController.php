<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Testimonial;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function testimonials()
    {
        return view('pages.testimonials', [
            'testimonials' => Testimonial::active()->get(),
        ]);
    }

    public function faq()
    {
        return view('pages.faq', [
            'faqs' => Faq::active()->get(),
        ]);
    }

    public function terms()
    {
        return view('pages.terms');
    }
}
