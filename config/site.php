<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site details
    |--------------------------------------------------------------------------
    |
    | Business details shown across the site — in the header and footer, on the
    | contact page, and in the LocalBusiness structured data in every page's
    | <head>. Read through the setting() helper, e.g. setting('phone').
    |
    | Edit these values, commit, and redeploy to change them.
    |
    */

    'site_name' => 'Brit Travel',
    'tagline' => 'Experience the ease and convenience of booking a coach with Brit Travel.',

    'phone' => env('SITE_PHONE', '+44 0000 000000'),
    'email' => env('SITE_EMAIL', 'info@brittravel.co.uk'),
    'address' => 'United Kingdom',

    /*
    |--------------------------------------------------------------------------
    | Booking notification address
    |--------------------------------------------------------------------------
    |
    | Where booking requests, quote requests, and contact messages are sent.
    | This is the inbox you check — set BOOKING_NOTIFICATION_EMAIL in the
    | environment to change it without touching code.
    |
    */

    'booking_notification_email' => env(
        'BOOKING_NOTIFICATION_EMAIL',
        env('SITE_EMAIL', 'info@brittravel.co.uk')
    ),

    /*
    |--------------------------------------------------------------------------
    | Homepage hero
    |--------------------------------------------------------------------------
    */

    'hero_heading' => 'Travel Together, Travel Better',
    'hero_subheading' => 'Modern coaches, professional drivers, and effortless booking — group travel across the UK made simple.',

    /*
    |--------------------------------------------------------------------------
    | Social links
    |--------------------------------------------------------------------------
    |
    | Leave blank to hide the icon.
    |
    */

    'facebook_url' => '',
    'instagram_url' => '',
    'whatsapp_number' => '',

];
