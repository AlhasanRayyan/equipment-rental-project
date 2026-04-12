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
        $users = User::all();
        $admins = User::where('role', 'admin')->get();
        $bookings = Booking::with(['renter', 'owner', 'equipment.owner'])->get();
        $equipments = Equipment::with('owner')->get();

        if ($users->isEmpty() || $bookings->isEmpty() || $equipments->isEmpty()) {
            $this->command->warn('Users or bookings or equipments are missing. Seed them first.');
            return;
        }

        $fullName = function ($user) {
            if (!$user) return null;
            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            return $name !== '' ? $name : null;
        };

        /*
        |--------------------------------------------------------------------------
        | إشعارات المستأجرين
        |--------------------------------------------------------------------------
        */
        foreach ($bookings->take(12) as $booking) {
            if (!$booking->equipment || !$booking->renter) {
                continue;
            }

            $equipment = $booking->equipment;
            $ownerName = $fullName($booking->owner ?? $equipment->owner);
            $renterName = $fullName($booking->renter);

            // 1) تم إرسال الطلب
            $booking->renter->notify(new AppAlertNotification(
                kind: 'booking_request',
                title: 'تم إرسال طلب الحجز',
                message: "تم إرسال طلب حجز للمعدة: {$equipment->name}" . ($ownerName ? "، بانتظار موافقة {$ownerName}" : ''),
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                ]
            ));

            // 2) تم تأكيد الحجز
            $booking->renter->notify(new AppAlertNotification(
                kind: 'booking_confirmed',
                title: 'تم تأكيد الحجز',
                message: "تم تأكيد حجز المعدة: {$equipment->name}",
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'amount' => $booking->total_cost,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                ]
            ));

            // 3) تم رفع وثيقة دفع
            $booking->renter->notify(new AppAlertNotification(
                kind: 'payment_received',
                title: 'تم إرسال وثيقة الدفع',
                message: "تم إرسال وثيقة الدفع الخاصة بالمعدة: {$equipment->name} وهي الآن قيد المراجعة.",
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'amount' => $booking->total_cost,
                ]
            ));

            // 4) قبول الوثيقة
            $booking->renter->notify(new AppAlertNotification(
                kind: 'payment_received',
                title: 'تم قبول وثيقة الدفع',
                message: "تم قبول وثيقة الدفع الخاصة بالمعدة: {$equipment->name}",
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'amount' => $booking->total_cost,
                ]
            ));

            // 5) رفض الوثيقة
            $booking->renter->notify(new AppAlertNotification(
                kind: 'payment_failed',
                title: 'تم رفض وثيقة الدفع',
                message: "تم رفض وثيقة الدفع الخاصة بالمعدة: {$equipment->name}",
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'reason' => 'الصورة غير واضحة أو البيانات غير مطابقة.',
                ]
            ));

            // 6) إلغاء الحجز
            $booking->renter->notify(new AppAlertNotification(
                kind: 'booking_cancelled',
                title: 'تم إلغاء الحجز',
                message: "تم إلغاء حجز المعدة: {$equipment->name}",
                url: route('renter.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                    'reason' => 'تم الإلغاء من طرف المالك.',
                ]
            ));

            // 7) رسالة جديدة
            $booking->renter->notify(new AppAlertNotification(
                kind: 'new_message',
                title: 'رسالة جديدة',
                message: $ownerName
                    ? "لديك رسالة جديدة من {$ownerName}"
                    : 'لديك رسالة جديدة بخصوص الحجز.',
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'sender_name' => $ownerName,
                ]
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | إشعارات المؤجرين
        |--------------------------------------------------------------------------
        */
        foreach ($bookings->take(12) as $booking) {
            if (!$booking->equipment || !$booking->owner) {
                continue;
            }

            $equipment = $booking->equipment;
            $renterName = $fullName($booking->renter);

            // 1) طلب حجز جديد
            $booking->owner->notify(new AppAlertNotification(
                kind: 'booking_request',
                title: 'طلب حجز جديد',
                message: "وصل طلب حجز جديد على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
                url: route('owner.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'renter_name' => $renterName,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                ]
            ));

            // 2) تم رفع وثيقة دفع
            $booking->owner->notify(new AppAlertNotification(
                kind: 'payment_received',
                title: 'تم رفع وثيقة الدفع',
                message: "قام المستأجر برفع وثيقة دفع للمعدة: {$equipment->name}" . ($renterName ? " ({$renterName})" : ''),
                url: route('owner.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'renter_name' => $renterName,
                    'amount' => $booking->total_cost,
                ]
            ));

            // 3) دفعة جديدة
            $booking->owner->notify(new AppAlertNotification(
                kind: 'payment_received',
                title: 'دفعة جديدة',
                message: "تم استلام دفعة جديدة على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
                url: route('owner.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'renter_name' => $renterName,
                    'amount' => $booking->total_cost,
                ]
            ));

            // 4) إلغاء حجز
            $booking->owner->notify(new AppAlertNotification(
                kind: 'booking_cancelled',
                title: 'تم إلغاء حجز على معدتك',
                message: "تم إلغاء حجز على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
                url: route('owner.payments.show', $booking->id),
                meta: [
                    'booking_id' => $booking->id,
                    'equipment_name' => $equipment->name,
                    'renter_name' => $renterName,
                    'reason' => 'تم الإلغاء أثناء المراجعة.',
                ]
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | إشعارات المعدات
        |--------------------------------------------------------------------------
        */
        foreach ($equipments->take(10) as $equipment) {
            $ownerName = $fullName($equipment->owner);

            // لصاحب المعدة
            if ($equipment->owner) {
                $equipment->owner->notify(new AppAlertNotification(
                    kind: 'equipment_created',
                    title: 'تمت إضافة المعدة بنجاح',
                    message: "تمت إضافة المعدة: {$equipment->name} بنجاح وأصبحت ضمن معداتك.",
                    url: route('equipments.show', $equipment->id),
                    meta: [
                        'equipment_name' => $equipment->name,
                        'owner_name' => $ownerName,
                    ]
                ));
            }

            // للمستخدمين الآخرين
            $otherUsers = User::where('role', '!=', 'admin')
                ->where('id', '!=', $equipment->owner_id)
                ->inRandomOrder()
                ->take(8)
                ->get();

            foreach ($otherUsers as $user) {
                $user->notify(new AppAlertNotification(
                    kind: 'equipment_created',
                    title: 'معدة جديدة متاحة',
                    message: "تمت إضافة معدة جديدة: {$equipment->name}" . ($ownerName ? " بواسطة {$ownerName}" : ''),
                    url: route('equipments.show', $equipment->id),
                    meta: [
                        'equipment_name' => $equipment->name,
                        'owner_name' => $ownerName,
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
            foreach ($bookings->take(12) as $booking) {
                if (!$booking->equipment) {
                    continue;
                }

                $equipment = $booking->equipment;
                $renterName = $fullName($booking->renter);
                $ownerName = $fullName($booking->owner ?? $equipment->owner);

                $admin->notify(new AppAlertNotification(
                    kind: 'booking_request',
                    title: 'طلب حجز جديد',
                    message: "تم إنشاء طلب حجز جديد على المعدة: {$equipment->name}" . ($renterName ? " بواسطة {$renterName}" : ''),
                    url: route('admin.bookings.show', $booking->id),
                    meta: [
                        'booking_id' => $booking->id,
                        'equipment_name' => $equipment->name,
                        'renter_name' => $renterName,
                        'owner_name' => $ownerName,
                    ]
                ));

                $admin->notify(new AppAlertNotification(
                    kind: 'booking_confirmed',
                    title: 'تأكيد حجز',
                    message: "تم تأكيد حجز المعدة: {$equipment->name}",
                    url: route('admin.bookings.show', $booking->id),
                    meta: [
                        'booking_id' => $booking->id,
                        'equipment_name' => $equipment->name,
                        'renter_name' => $renterName,
                        'owner_name' => $ownerName,
                    ]
                ));

                $admin->notify(new AppAlertNotification(
                    kind: 'booking_cancelled',
                    title: 'إلغاء حجز',
                    message: "تم إلغاء حجز المعدة: {$equipment->name}",
                    url: route('admin.bookings.show', $booking->id),
                    meta: [
                        'booking_id' => $booking->id,
                        'equipment_name' => $equipment->name,
                        'renter_name' => $renterName,
                        'owner_name' => $ownerName,
                        'reason' => 'سبب تجريبي',
                    ]
                ));

                $admin->notify(new AppAlertNotification(
                    kind: 'payment_received',
                    title: 'تم استلام دفعة',
                    message: "تم استلام دفعة جديدة خاصة بالمعدة: {$equipment->name}",
                    url: route('admin.bookings.show', $booking->id),
                    meta: [
                        'booking_id' => $booking->id,
                        'equipment_name' => $equipment->name,
                        'renter_name' => $renterName,
                        'owner_name' => $ownerName,
                        'amount' => $booking->total_cost,
                    ]
                ));

                $admin->notify(new AppAlertNotification(
                    kind: 'payment_failed',
                    title: 'تم رفض وثيقة دفع',
                    message: "تم رفض وثيقة دفع خاصة بالمعدة: {$equipment->name}",
                    url: route('admin.bookings.show', $booking->id),
                    meta: [
                        'booking_id' => $booking->id,
                        'equipment_name' => $equipment->name,
                        'renter_name' => $renterName,
                        'owner_name' => $ownerName,
                        'reason' => 'الوثيقة غير واضحة',
                    ]
                ));
            }

            foreach ($equipments->take(8) as $equipment) {
                $ownerName = $fullName($equipment->owner);

                $admin->notify(new AppAlertNotification(
                    kind: 'equipment_created',
                    title: 'تمت إضافة معدة جديدة',
                    message: "تمت إضافة معدة جديدة: {$equipment->name}" . ($ownerName ? " بواسطة {$ownerName}" : ''),
                    url: route('equipments.show', $equipment->id),
                    meta: [
                        'equipment_name' => $equipment->name,
                        'owner_name' => $ownerName,
                    ]
                ));
            }
        }

        $this->command->info('Notifications seeded successfully with realistic data.');
    }
}
