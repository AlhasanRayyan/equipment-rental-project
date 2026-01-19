<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_tracking', function (Blueprint $table) {
            // إضافة الحقول الجديدة
            $table->dateTime('start_time')->nullable()->after('status');
            $table->dateTime('end_time')->nullable()->after('start_time');
            // مدة التشغيل (ساعات) - استخدمت decimal لتخزين الكسور مثل 2.5 ساعة
            $table->decimal('duration', 8, 2)->nullable()->after('end_time'); 
        });
    }

    public function down(): void
    {
        Schema::table('equipment_tracking', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'duration']);
        });
    }
};