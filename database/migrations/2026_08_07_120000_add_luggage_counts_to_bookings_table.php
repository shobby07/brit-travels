<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Collected alongside the passenger count so we can send the right
            // sized vehicle. Default 0 keeps existing bookings valid.
            $table->unsignedSmallInteger('luggage_small')->default(0);
            $table->unsignedSmallInteger('bags_medium')->default(0);
            $table->unsignedSmallInteger('luggage_large')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['luggage_small', 'bags_medium', 'luggage_large']);
        });
    }
};
