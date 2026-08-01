<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coach_hire_locations', function (Blueprint $table) {
            $table->string('hero_image_credit', 500)->nullable()->after('hero_image_alt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coach_hire_locations', function (Blueprint $table) {
            $table->dropColumn('hero_image_credit');
        });
    }
};
