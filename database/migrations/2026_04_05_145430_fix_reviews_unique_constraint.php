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
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);

            $table->dropUnique(['booking_id']);

            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();

            $table->unique(['booking_id', 'reviewer_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
