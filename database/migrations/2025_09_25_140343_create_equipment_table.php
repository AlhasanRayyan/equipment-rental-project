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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id(); // equipment_id
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('equipment_categories')->onDelete('restrict');
            $table->string('name');
            $table->text('description');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('location_latitude', 10, 7)->nullable();
            $table->decimal('location_longitude', 10, 7)->nullable();
            $table->string('location_address')->nullable();
            $table->enum('status', ['available', 'rented', 'maintenance', 'unavailable'])->default('available');
            $table->boolean('is_approved_by_admin')->default(false);
            $table->boolean('has_gps_tracker')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->date('last_maintenance_date')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->timestamps(); // created_at (listing_date), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
