<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use App\Notifications\AppAlertNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class);
        }

        if (Booking::count() === 0) {
            $this->call(BookingSeeder::class);
        }

        $users = User::all();
        $bookings = Booking::with(['renter', 'owner', 'equipment'])->get();
        $equipments = Equipment::with('owner')->get();
        $admins = User::where('role', 'admin')->get();

        $kinds = [
            'booking_request',
            'booking_confirmed',
            'booking_cancelled',
            'payment_received',
            'payment_failed',
            'refund_issued',
            'new_message',
            'equipment_moved',
            'system_alert',
        ];

        $titles = [
            'booking_request'   => 'طلب حجز جديد',
            'booking_confirmed' => 'تم تأكيد الحجز',
            'booking_cancelled' => 'تم إلغاء الحجز',
            'payment_received'  => 'تم استلام الدفع',
            'payment_failed'    => 'فشل الدفع',
            'refund_issued'     => 'تم إصدار استرداد',
            'new_message'       => 'رسالة جديدة',
            'equipment_moved'   => 'تحذير حركة',
            'system_alert'      => 'إشعار',
        ];

        /*
        |--------------------------------------------------------------------------
        | إشعارات عامة تجريبية لكل مستخدم
        |--------------------------------------------------------------------------
        */
        foreach ($users as $user) {
            $count = rand(2, 5);

            for ($i = 0; $i < $count; $i++) {
                $kind = $kinds[array_rand($kinds)];

                $meta = [];
                if ($kind === 'system_alert') {
                    $meta = [
                        'login_at' => now()->subMinutes(rand(1, 500))->toDateTimeString(),
                    ];
                }

                $user->notify(new AppAlertNotification(
                    kind: $kind,
                    title: $titles[$kind],
                    message: "إشعار تجريبي: {$titles[$kind]}",
                    meta: $meta
                ));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | إشعارات مرتبطة بالحجوزات
        |--------------------------------------------------------------------------
        */
        foreach ($bookings as $booking) {
            $equipment = $booking->equipment;
            if (!$equipment) {
                continue;
            }

            // للمستأجر: تم إرسال الطلب
            if ($booking->renter) {
                $booking->renter->notify(new AppAlertNotification(
                    kind: 'booking_request',
                    title: 'تم إرسال طلب الحجز',
                    message: "تم إرسال طلب الحجز #{$booking->id} للمعدة: {$equipment->name} وهو الآن بانتظار الموافقة.",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'owner_id'     => $booking->owner_id,
                        'renter_id'    => $booking->renter_id,
                    ]
                ));
            }

            // للمؤجر: طلب حجز جديد
            if ($booking->owner) {
                $booking->owner->notify(new AppAlertNotification(
                    kind: 'booking_request',
                    title: 'طلب حجز جديد',
                    message: "وصل طلب حجز جديد على المعدة: {$equipment->name} (حجز #{$booking->id})",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'owner_id'     => $booking->owner_id,
                        'renter_id'    => $booking->renter_id,
                    ]
                ));
            }

            // للمستأجر: تأكيد الحجز
            if ($booking->renter) {
                $booking->renter->notify(new AppAlertNotification(
                    kind: 'booking_confirmed',
                    title: 'تم تأكيد الحجز',
                    message: "تم تأكيد الحجز #{$booking->id} للمعدة: {$equipment->name}",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                    ]
                ));
            }

            // للمؤجر: دفعة جديدة
            if ($booking->owner) {
                $booking->owner->notify(new AppAlertNotification(
                    kind: 'payment_received',
                    title: 'دفعة جديدة',
                    message: "تم استلام دفعة للحجز #{$booking->id} على معدتك {$equipment->name} بقيمة {$booking->total_cost}",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'amount'       => $booking->total_cost,
                    ]
                ));
            }

            // للمستأجر: رسالة جديدة
            if ($booking->renter && rand(0, 1)) {
                $booking->renter->notify(new AppAlertNotification(
                    kind: 'new_message',
                    title: 'رسالة جديدة',
                    message: "لديك رسالة جديدة بخصوص الحجز #{$booking->id}",
                    meta: [
                        'booking_id'      => $booking->id,
                        'conversation_id' => rand(1, 20),
                        'message_id'      => rand(100, 999),
                    ]
                ));
            }

            // إلغاء تجريبي لبعض الحجوزات
            if ($booking->renter && $booking->owner && rand(0, 1)) {
                $reason = 'إلغاء تجريبي من السيدر';

                $booking->renter->notify(new AppAlertNotification(
                    kind: 'booking_cancelled',
                    title: 'تم إلغاء الحجز',
                    message: "تم إلغاء الحجز #{$booking->id} للمعدة: {$equipment->name} (السبب: {$reason})",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'reason'       => $reason,
                    ]
                ));

                $booking->owner->notify(new AppAlertNotification(
                    kind: 'booking_cancelled',
                    title: 'تم إلغاء حجز على معدتك',
                    message: "تم إلغاء الحجز #{$booking->id} على المعدة: {$equipment->name} (السبب: {$reason})",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'reason'       => $reason,
                    ]
                ));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | إشعارات المعدات
        |--------------------------------------------------------------------------
        */
        foreach ($equipments->take(5) as $equipment) {
            if ($equipment->owner) {
                $equipment->owner->notify(new AppAlertNotification(
                    kind: 'system_alert',
                    title: 'تمت إضافة المعدة بنجاح',
                    message: "تمت إضافة المعدة: {$equipment->name} بنجاح وأصبحت ضمن معداتك.",
                    meta: [
                        'equipment_id' => $equipment->id,
                        'owner_id'     => $equipment->owner_id,
                    ]
                ));

                $equipment->owner->notify(new AppAlertNotification(
                    kind: 'equipment_moved',
                    title: 'تحذير حركة',
                    message: "تحركت المعدة {$equipment->name} لمسافة تقريباً " . number_format(rand(1, 20) / 3, 3) . " كم",
                    meta: [
                        'equipment_id' => $equipment->id,
                        'distance_km'  => rand(1, 20) / 3,
                        'lat'          => 31.95 + (rand(-100, 100) / 1000),
                        'lng'          => 35.91 + (rand(-100, 100) / 1000),
                    ]
                ));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | إشعارات الأدمن
        |--------------------------------------------------------------------------
        */
        foreach ($admins as $admin) {
            foreach ($bookings->take(5) as $booking) {
                $equipment = $booking->equipment;
                if (!$equipment) {
                    continue;
                }

                $admin->notify(new AppAlertNotification(
                    kind: 'booking_request',
                    title: 'طلب حجز جديد',
                    message: "تم إنشاء حجز جديد #{$booking->id} على {$equipment->name}",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                    ]
                ));

                $admin->notify(new AppAlertNotification(
                    kind: 'payment_received',
                    title: 'تم استلام دفعة',
                    message: "دفعة جديدة للحجز #{$booking->id} بقيمة {$booking->total_cost}",
                    meta: [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $equipment->id,
                        'amount'       => $booking->total_cost,
                    ]
                ));
            }

            foreach ($equipments->take(3) as $equipment) {
                $admin->notify(new AppAlertNotification(
                    kind: 'system_alert',
                    title: 'تمت إضافة معدة جديدة',
                    message: "تمت إضافة معدة جديدة: {$equipment->name} (رقم #{$equipment->id})",
                    meta: [
                        'equipment_id' => $equipment->id,
                        'owner_id'     => $equipment->owner_id,
                    ]
                ));
            }
        }

        $this->command->info('Notifications seeded successfully using AppAlertNotification.');
    }
}