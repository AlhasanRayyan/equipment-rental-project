<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Conversation;
use App\Notifications\AppAlertNotification;

class NotificationService
{
    public static function admins()
    {
        return User::where('role', 'admin')->get();
    }

    public static function nonOwnerUsers(?int $excludeUserId = null)
    {
        return User::where('role', '!=', 'admin')
            ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
            ->get();
    }

    public static function toAdmins(AppAlertNotification $notification): void
    {
        foreach (self::admins() as $admin) {
            $admin->notify($notification);
        }
    }

    protected static function fullName(?User $user): ?string
    {
        if (!$user) return null;

        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        return $name !== '' ? $name : null;
    }

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    public static function systemAlert(
        User $user,
        string $title,
        string $message,
        ?string $url = null,
        array $meta = []
    ): void {
        $user->notify(new AppAlertNotification(
            kind: 'system_alert',
            title: $title,
            message: $message,
            url: $url,
            meta: $meta
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */

    // للمؤجر + الأدمن
    public static function bookingRequest(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_request',
            title: 'طلب حجز جديد',
            message: "وصل طلب حجز جديد على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
            url: $ownerUrl,
            meta: [
                'booking_id' => $booking->id, // داخلي فقط
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_request',
            title: 'طلب حجز جديد',
            message: "تم إنشاء طلب حجز جديد على المعدة: {$equipment->name}" . ($renterName ? " بواسطة {$renterName}" : ''),
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));
    }

    // للمستأجر
    public static function bookingCreatedByRenter(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');

        $ownerName = self::fullName($equipment->owner);
        $renterUrl = route('renter.payments.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_request',
            title: 'تم إرسال طلب الحجز',
            message: "تم إرسال طلب الحجز للمعدة: {$equipment->name}" . ($ownerName ? "، بانتظار موافقة {$ownerName}" : ''),
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));
    }

    // للمستأجر + الأدمن
    public static function bookingConfirmed(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تم تأكيد الحجز',
            message: "تم تأكيد حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'amount' => $booking->total_cost,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تأكيد حجز',
            message: "تم تأكيد حجز المعدة: {$equipment->name}" . ($renterName ? " للمستخدم {$renterName}" : ''),
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'amount' => $booking->total_cost,
            ]
        ));
    }

    // للمستأجر + المؤجر + الأدمن
    public static function bookingCancelled(Booking $booking, Equipment $equipment, ?string $reason = null): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $reasonText = $reason ? " (السبب: {$reason})" : "";
        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        // $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'تم إلغاء الحجز',
            message: "تم إلغاء حجز المعدة: {$equipment->name}{$reasonText}",
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));

        // $equipment->owner?->notify(new AppAlertNotification(
        //     kind: 'booking_cancelled',
        //     title: 'تم إلغاء حجز على معدتك',
        //     message: "تم إلغاء حجز على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : '') . $reasonText,
        //     url: $ownerUrl,
        //     meta: [
        //         'booking_id' => $booking->id,
        //         'equipment_name' => $equipment->name,
        //         'renter_name' => $renterName,
        //         'reason' => $reason,
        //     ]
        // ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'إلغاء حجز',
            message: "تم إلغاء حجز المعدة: {$equipment->name}{$reasonText}",
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));
    }

    // للمستأجر + المؤجر + الأدمن
    public static function bookingCompleted(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        // $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'انتهى الحجز',
            message: "انتهى حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
            ]
        ));

        // $equipment->owner?->notify(new AppAlertNotification(
        //     kind: 'booking_confirmed',
        //     title: 'تم إنهاء حجز على معدتك',
        //     message: "انتهى حجز المعدة: {$equipment->name}" . ($renterName ? " للمستخدم {$renterName}" : ''),
        //     url: $ownerUrl,
        //     meta: [
        //         'booking_id' => $booking->id,
        //         'equipment_name' => $equipment->name,
        //         'renter_name' => $renterName,
        //     ]
        // ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'انتهاء حجز',
            message: "انتهى حجز المعدة: {$equipment->name}",
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
            ]
        ));
    }

    // للمستأجر
    public static function bookingStartingSoon(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');

        $ownerName = self::fullName($equipment->owner);
        $renterUrl = route('renter.payments.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'اقتراب موعد الحجز',
            message: "اقترب موعد حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date ?? null,
            ]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Equipment
    |--------------------------------------------------------------------------
    */

    // لصاحب المعدة
    public static function equipmentCreatedForOwner(Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::fullName($equipment->owner);

        $equipment->owner?->notify(new AppAlertNotification(
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

    // للمستخدمين غير صاحب المعدة + الأدمن
    public static function equipmentPublished(Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::fullName($equipment->owner);
        $equipmentUrl = route('equipments.show', $equipment->id);

        foreach (self::nonOwnerUsers($equipment->owner_id) as $user) {
            $user->notify(new AppAlertNotification(
                kind: 'equipment_created',
                title: 'معدة جديدة متاحة',
                message: "تمت إضافة معدة جديدة: {$equipment->name}" . ($ownerName ? " بواسطة {$ownerName}" : ''),
                url: $equipmentUrl,
                meta: [
                    'equipment_name' => $equipment->name,
                    'owner_name' => $ownerName,
                ]
            ));
        }

        self::toAdmins(new AppAlertNotification(
            kind: 'equipment_created',
            title: 'تمت إضافة معدة جديدة',
            message: "تمت إضافة معدة جديدة: {$equipment->name}" . ($ownerName ? " بواسطة {$ownerName}" : ''),
            url: $equipmentUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
            ]
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | GPS
    |--------------------------------------------------------------------------
    */

    public static function equipmentMoved(Equipment $equipment, float $distanceKm, float $lat, float $lng): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::fullName($equipment->owner);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'equipment_moved',
            title: 'تحذير حركة',
            message: "تحركت المعدة {$equipment->name} لمسافة تقريباً " . number_format($distanceKm, 3) . " كم",
            meta: [
                'equipment_name' => $equipment->name,
                'distance_km' => $distanceKm,
                'location_text' => "({$lat}, {$lng})",
                'owner_name' => $ownerName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'equipment_moved',
            title: 'تحذير حركة (GPS)',
            message: "تحركت المعدة: {$equipment->name}",
            meta: [
                'equipment_name' => $equipment->name,
                'distance_km' => $distanceKm,
                'location_text' => "({$lat}, {$lng})",
                'owner_name' => $ownerName,
            ]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public static function paymentReceived(Booking $booking, Equipment $equipment, float $amount): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم استلام الدفع',
            message: "تم استلام دفعة خاصة بحجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'owner_name' => $ownerName,
            ]
        ));

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'دفعة جديدة',
            message: "تم استلام دفعة جديدة على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
            url: $ownerUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'renter_name' => $renterName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم استلام دفعة',
            message: "تم استلام دفعة جديدة خاصة بالمعدة: {$equipment->name}",
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
            ]
        ));
    }

    public static function paymentProofSubmitted(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم رفع وثيقة الدفع',
            message: "قام المستأجر برفع وثيقة دفع للمعدة: {$equipment->name}" . ($renterName ? " ({$renterName})" : ''),
            url: $ownerUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم رفع وثيقة دفع',
            message: "تم رفع وثيقة دفع خاصة بالمعدة: {$equipment->name}",
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
            ]
        ));
    }

    public static function paymentApproved(Booking $booking, Equipment $equipment, float $amount): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::fullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم قبول وثيقة الدفع',
            message: "تم قبول وثيقة الدفع الخاصة بالمعدة: {$equipment->name}",
            url: route('renter.payments.show', $booking->id),
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'owner_name' => $ownerName,
            ]
        ));
    }

    public static function paymentRejected(Booking $booking, Equipment $equipment, ?string $reason = null): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $reasonText = $reason ? " (السبب: {$reason})" : "";
        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'تم رفض وثيقة الدفع',
            message: "تم رفض وثيقة الدفع الخاصة بالمعدة: {$equipment->name}{$reasonText}",
            url: route('renter.payments.show', $booking->id),
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'تم رفض وثيقة دفع',
            message: "تم رفض وثيقة الدفع الخاصة بالمعدة: {$equipment->name}{$reasonText}",
            url: route('admin.bookings.show', $booking->id),
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));
    }

    public static function cashPaymentSelected(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::fullName($booking->renter);
        $ownerName = self::fullName($equipment->owner);

        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم اختيار الدفع النقدي',
            message: "اختار المستأجر الدفع النقدي للمعدة: {$equipment->name}" . ($renterName ? " ({$renterName})" : ''),
            url: $ownerUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'amount' => $booking->total_cost,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم اختيار الدفع النقدي',
            message: "تم اختيار الدفع النقدي للمعدة: {$equipment->name}",
            url: $adminUrl,
            meta: [
                'booking_id' => $booking->id,
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'amount' => $booking->total_cost,
            ]
        ));
    }

}
