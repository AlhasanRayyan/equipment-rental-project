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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // مالك المعدة
            $table->foreignId('renter_id')->constrained('users')->onDelete('cascade'); // المستأجر الذي يبدأ المحادثة
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->onDelete('cascade'); // المعدة التي تدور عنها المحادثة (قد تكون null)
            $table->timestamp('last_message_at')->nullable(); // لتسهيل ترتيب المحادثات حسب آخر رسالة
            $table->timestamps(); // created_at, updated_at

            $table->unique(['owner_id', 'renter_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};