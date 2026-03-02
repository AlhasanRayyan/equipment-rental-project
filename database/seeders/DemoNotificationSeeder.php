<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use App\Notifications\AppAlertNotification;

class DemoNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admins = User::where('role', 'admin')->get();
        $users  = User::where('role', 'user')->get();

        $equipments = Equipment::with('owner')->get();
        $bookings   = Booking::with(['equipment', 'renter', 'owner'])->get();

        // ===========================
        //  إشعارات الأدمن
        // ===========================
        foreach ($admins as $admin) {

            $admin->notify(new AppAlertNotification(
                kind: 'system_alert',
                title: 'تنبيه من النظام',
                message: 'تم إنشاء حساب جديد في النظام',
                meta: []
            ));

            $admin->notify(new AppAlertNotification(
                kind: 'payment_failed',
                title: 'فشل عملية دفع',
                message: 'فشل دفع للحجز #12',
                meta: ['booking_id' => 12]
            ));

            $admin->notify(new AppAlertNotification(
                kind: 'booking_request',
                title: 'طلب حجز جديد',
                message: 'تم إنشاء طلب حجز جديد على النظام',
                meta: ['booking_id' => 1]
            ));
        }

        // ===========================
        //  إشعارات المالك (Owner)
        // ===========================
        foreach ($equipments as $eq) {

            if (!$eq->owner) continue;

            $eq->owner->notify(new AppAlertNotification(
                kind: 'equipment_moved',
                title: 'تحركت المعدة',
                message: "تم تحريك المعدة {$eq->name}",
                meta: [
                    'equipment_id' => $eq->id,
                    'distance_km' => 0.125,
                    'lat' => 31.5,
                    'lng' => 34.46
                ]
            ));

            $eq->owner->notify(new AppAlertNotification(
                kind: 'low_battery',
                title: 'بطارية منخفضة',
                message: "بطارية جهاز {$eq->name} منخفضة",
                meta: [
                    'equipment_id' => $eq->id,
                    'battery_level' => 20
                ]
            ));
        }

        // ===========================
        //  إشعارات الحجوزات
        // ===========================
        foreach ($bookings as $b) {

            if (!$b->equipment || !$b->renter) continue;

            // Owner
            if ($b->owner) {
                $b->owner->notify(new AppAlertNotification(
                    kind: 'booking_request',
                    title: 'طلب حجز جديد',
                    message: "طلب حجز على المعدة {$b->equipment->name}",
                    meta: [
                        'booking_id' => $b->id,
                        'equipment_id' => $b->equipment->id
                    ]
                ));
            }

            // Renter
            $b->renter->notify(new AppAlertNotification(
                kind: 'booking_confirmed',
                title: 'تم تأكيد الحجز',
                message: "تم تأكيد حجزك #{$b->id}",
                meta: ['booking_id' => $b->id]
            ));

            $b->renter->notify(new AppAlertNotification(
                kind: 'booking_cancelled',
                title: 'تم إلغاء الحجز',
                message: "تم إلغاء الحجز #{$b->id}",
                meta: ['booking_id' => $b->id]
            ));

            // رسائل
            $b->renter->notify(new AppAlertNotification(
                kind: 'new_message',
                title: 'رسالة جديدة',
                message: "لديك رسالة جديدة",
                meta: ['conversation_id' => 1]
            ));

            // دفع
            if ($b->owner) {
                $b->owner->notify(new AppAlertNotification(
                    kind: 'payment_received',
                    title: 'تم استلام دفعة',
                    message: "تم استلام دفعة للحجز #{$b->id}",
                    meta: ['booking_id' => $b->id]
                ));
            }
        }

        $this->command->info('All notification types inserted successfully ');
    }
}
