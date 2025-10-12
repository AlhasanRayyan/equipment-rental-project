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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // review_id
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->unsignedTinyInteger('rating_overall'); // assuming 1-5 rating
            $table->text('comment')->nullable();
            $table->unsignedTinyInteger('owner_communication_rating')->nullable();
            $table->unsignedTinyInteger('equipment_condition_rating')->nullable();
            $table->unsignedTinyInteger('renter_punctuality_rating')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('owner_response')->nullable();
            $table->timestamp('owner_response_date')->nullable();
            $table->timestamps(); // created_at (review_date), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
