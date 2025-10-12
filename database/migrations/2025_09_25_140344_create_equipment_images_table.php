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
        Schema::create('equipment_images', function (Blueprint $table) {
            $table->id(); // image_id
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('image_url');
            $table->boolean('is_main')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps(); // created_at (uploaded_at), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_images');
    }
};
