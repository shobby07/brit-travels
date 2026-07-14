<?php

use Illuminate\Support\Facades\Schedule;

// Process queued emails every minute. On SiteGround, a single cron entry
// running `php artisan schedule:run` drives this.
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();
