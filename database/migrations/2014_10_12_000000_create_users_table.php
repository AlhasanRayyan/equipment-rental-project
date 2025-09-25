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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('profile_picture_url')->nullable();
            $table->text('description')->nullable();
            $table->string('location_text')->nullable();
            $table->boolean('is_active')->default(true);
             $table->timestamp('last_login')->nullable();
            $table->decimal('average_owner_rating', 3, 2)->default(0.00);
            $table->decimal('average_renter_rating', 3, 2)->default(0.00);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
