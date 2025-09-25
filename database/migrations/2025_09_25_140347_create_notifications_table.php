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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // notification_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'booking_request',
                'booking_confirmed',
                'booking_cancelled',
                'payment_received',
                'payment_failed',
                'refund_issued',
                'new_message',
                'equipment_approved',
                'equipment_rejected',
                'review_received',
                'system_alert'
            ]);
            $table->text('message');
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of booking, equipment, etc.
            $table->boolean('is_read')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps(); // created_at (timestamp), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
