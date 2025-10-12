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
        Schema::create('equipment_tracking', function (Blueprint $table) {
            $table->id(); // location_id
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 6, 2)->default(0.00); // e.g., km/h or mph
            $table->decimal('battery_level', 5, 2)->nullable(); // e.g., percentage 0-100
            $table->enum('status', ['online', 'offline', 'moving', 'idle'])->default('online');
            $table->timestamps(); // created_at (timestamp), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_tracking');
    }
};
