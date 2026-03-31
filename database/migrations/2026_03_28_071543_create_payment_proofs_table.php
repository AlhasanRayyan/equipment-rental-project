<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('renter_id')->constrained('users')->cascadeOnDelete();

            // بيانات التحويل
            $table->decimal('transferred_amount', 10, 2);
            $table->string('bank_or_wallet_name');       // اسم البنك أو المحفظة (إلزامي)
            $table->string('proof_image');               // مسار صورة الإشعار
            $table->text('notes')->nullable();           // ملاحظات إضافية

            // حالة المراجعة
            $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        // إضافة عمود payment_method إلى payments إذا لم يكن موجوداً
        // وعمود لربط proof
        Schema::table('payments', function (Blueprint $table) {
            // إضافة الحقول فقط إذا لم تكن موجودة
            if (!Schema::hasColumn('payments', 'payment_method_type')) {
                $table->enum('payment_method_type', ['cash', 'bank_transfer', 'wallet'])
                      ->default('cash')
                      ->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method_type');
        });
    }
};