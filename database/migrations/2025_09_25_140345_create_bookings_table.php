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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // booking_id
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('renter_id')->constrained('users')->onDelete('cascade');
            // owner_id can be fetched via equipment->owner_id, but keeping it as per diagram
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('rental_duration_days');
            $table->enum('rental_rate_type', ['daily', 'weekly', 'monthly']);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('deposit_amount_paid', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->enum('booking_status', ['pending', 'confirmed', 'active', 'completed', 'cancelled'])->default('pending');
            $table->string('pickup_location')->nullable();
            $table->string('return_location')->nullable();
            $table->string('contract_url')->nullable();
            $table->text('special_requirements')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps(); // created_at (booking_date), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
