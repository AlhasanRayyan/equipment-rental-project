<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use App\Notifications\GenericDatabaseNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) $this->call(UserSeeder::class);
        if (Booking::count() === 0) $this->call(BookingSeeder::class);
        if (Equipment::count() === 0) $this->call(EquipmentSeeder::class);

        $users = User::all();
        $bookings = Booking::with(['renter','owner','equipment'])->get();
        $equipments = Equipment::all();

        $types = [
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
        ];

        // إشعارات عامة لكل مستخدم
        foreach ($users as $user) {
            $count = rand(3, 8);
            for ($i = 0; $i < $count; $i++) {
                $kind = $types[array_rand($types)];
                $user->notify(new GenericDatabaseNotification(
                    kind: $kind,
                    message: "إشعار تجريبي: {$kind}"
                ));
            }
        }

        // إشعارات مرتبطة بالحجوزات
        foreach ($bookings as $booking) {
            // للمستأجر
            $booking->renter->notify(new GenericDatabaseNotification(
                kind: 'booking_confirmed',
                message: "تم تأكيد الحجز #{$booking->id} للمعدة {$booking->equipment->name}",
                url: route('admin.bookings.show', $booking->id),
                extra: ['booking_id' => $booking->id]
            ));

            if (rand(0,1)) {
                $booking->renter->notify(new GenericDatabaseNotification(
                    kind: 'new_message',
                    message: "لديك رسالة جديدة بخصوص الحجز #{$booking->id}",
                    url: route('admin.bookings.show', $booking->id),
                    extra: ['booking_id' => $booking->id]
                ));
            }

            // للمالك
            $booking->owner->notify(new GenericDatabaseNotification(
                kind: 'booking_request',
                message: "طلب حجز جديد للمعدة {$booking->equipment->name} (حجز #{$booking->id})",
                url: route('admin.bookings.show', $booking->id),
                extra: ['booking_id' => $booking->id]
            ));

            $booking->owner->notify(new GenericDatabaseNotification(
                kind: 'payment_received',
                message: "تم استلام دفعة للحجز #{$booking->id}",
                url: route('admin.bookings.show', $booking->id),
                extra: ['booking_id' => $booking->id]
            ));
        }

        // system alerts
        $randomUser = $users->random();
        for ($i=0; $i<5; $i++) {
            $randomUser->notify(new GenericDatabaseNotification(
                kind: 'system_alert',
                message: 'تنبيه نظام تجريبي'
            ));
        }

        // equipment approved example
        if ($equipments->count() > 0) {
            $u = User::where('role','user')->inRandomOrder()->first() ?? $users->random();
            $eq = $equipments->random();

            $u->notify(new GenericDatabaseNotification(
                kind: 'equipment_approved',
                message: "تمت الموافقة على المعدة {$eq->name}",
                extra: ['equipment_id' => $eq->id]
            ));
        }

        $this->command->info('Notifications seeded (Laravel database notifications)!');
    }
}
