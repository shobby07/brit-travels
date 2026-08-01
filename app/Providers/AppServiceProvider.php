<?php

namespace App\Providers;

use App\Models\CoachHireLocation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share active coach-hire locations with the navbar dropdown so new
        // cities appear automatically without touching the Blade template.
        View::composer('components.navbar', function ($view) {
            $locations = Schema::hasTable('coach_hire_locations')
                ? CoachHireLocation::active()->get(['name', 'slug'])
                : collect();

            $view->with('navLocations', $locations);
        });
    }
}
