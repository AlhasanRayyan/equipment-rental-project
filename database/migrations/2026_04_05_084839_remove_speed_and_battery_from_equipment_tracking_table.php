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
        Schema::table('equipment_tracking', function (Blueprint $table) {
            $table->dropColumn(['speed', 'battery_level']);
        });
    }

    public function down(): void
    {
        Schema::table('equipment_tracking', function (Blueprint $table) {
            $table->decimal('speed', 6, 2)->default(0.00);
            $table->decimal('battery_level', 5, 2)->nullable();
        });
    }
};