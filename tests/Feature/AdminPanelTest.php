<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect();
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_view_dashboard_and_resources(): void
    {
        $admin = User::factory()->create();

        foreach (['/admin', '/admin/bookings', '/admin/quotes', '/admin/coaches', '/admin/testimonials', '/admin/faqs', '/admin/manage-settings'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_admin_sees_booking_in_table(): void
    {
        $admin = User::factory()->create();

        $booking = Booking::create([
            'reference' => 'BT-2026-ADMIN',
            'trip_type' => 'one_way',
            'pickup_location' => 'Bristol',
            'dropoff_location' => 'Cardiff',
            'pickup_date' => now()->addDays(3),
            'pickup_time' => '10:00',
            'passengers' => 12,
            'name' => 'Admin View Test',
            'email' => 'x@y.z',
            'phone' => '07999',
            'status' => Booking::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get('/admin/bookings')
            ->assertOk()
            ->assertSee('BT-2026-ADMIN');
    }
}
