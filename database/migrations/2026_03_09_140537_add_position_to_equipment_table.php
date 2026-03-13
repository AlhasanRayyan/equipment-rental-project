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
        Schema::table('equipment', function (Blueprint $table) {
            $table->enum('position', [
                'north_gaza',     // شمال غزة
                'gaza_city',      // غزة
                'middle_area',    // الوسطى
                'deir_al_balah',  // دير البلح
                'khan_younis',    // خانيونس
                'rafah',          // رفح
            ])->nullable()->after('location_address');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
