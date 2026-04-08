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

    public static function toAdmins(AppAlertNotification $notification): void
    {
        foreach (self::admins() as $admin) {
            $admin->notify($notification);
        }
    }

    protected static function userFullName(?User $user): ?string
    {
        if (!$user) return null;
        return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */

    public static function bookingRequest(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $ownerUrl = route('owner.bookings.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_request',
            title: 'طلب حجز جديد',
            message: "وصل طلب حجز جديد على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : ''),
            url: $ownerUrl,
            meta: [
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
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));
    }

    public static function bookingCreatedByRenter(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');

        $ownerName = self::userFullName($equipment->owner);
        $renterUrl = route('renter.payments.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_request',
            title: 'تم إرسال طلب الحجز',
            message: "تم إرسال طلب الحجز للمعدة: {$equipment->name}" . ($ownerName ? "، بانتظار موافقة {$ownerName}" : ''),
            url: $renterUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
            ]
        ));
    }

    public static function bookingConfirmed(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تم تأكيد الحجز',
            message: "تم تأكيد حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'amount' => $booking->total_cost,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تأكيد حجز',
            message: "تم تأكيد حجز المعدة: {$equipment->name}" . ($renterName ? " للمستخدم {$renterName}" : ''),
            url: $adminUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'amount' => $booking->total_cost,
            ]
        ));
    }

    public static function bookingCancelled(Booking $booking, Equipment $equipment, ?string $reason = null): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $reasonText = $reason ? " (السبب: {$reason})" : "";
        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $ownerUrl = route('owner.bookings.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'تم إلغاء الحجز',
            message: "تم إلغاء حجز المعدة: {$equipment->name}{$reasonText}",
            url: $renterUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'تم إلغاء حجز على معدتك',
            message: "تم إلغاء حجز على المعدة: {$equipment->name}" . ($renterName ? " من {$renterName}" : '') . $reasonText,
            url: $ownerUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'reason' => $reason,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'إلغاء حجز',
            message: "تم إلغاء حجز المعدة: {$equipment->name}{$reasonText}",
            url: $adminUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));
    }

    public static function bookingCompleted(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $ownerUrl = route('owner.bookings.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'انتهى الحجز',
            message: "انتهى حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
            ]
        ));

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تم إنهاء حجز على معدتك',
            message: "انتهى حجز المعدة: {$equipment->name}" . ($renterName ? " للمستخدم {$renterName}" : ''),
            url: $ownerUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'انتهاء حجز',
            message: "انتهى حجز المعدة: {$equipment->name}",
            url: $adminUrl,
            meta: [
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
            ]
        ));
    }

    public static function bookingStartingSoon(Booking $booking, Equipment $equipment): void
    {
        $equipment->loadMissing('owner');

        $ownerName = self::userFullName($equipment->owner);
        $renterUrl = route('renter.payments.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'اقتراب موعد الحجز',
            message: "اقترب موعد حجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
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

    public static function equipmentCreated(Equipment $equipment): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::userFullName($equipment->owner);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'system_alert',
            title: 'تمت إضافة المعدة بنجاح',
            message: "تمت إضافة المعدة: {$equipment->name} بنجاح وأصبحت ضمن معداتك.",
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'system_alert',
            title: 'تمت إضافة معدة جديدة',
            message: "تمت إضافة معدة جديدة: {$equipment->name}" . ($ownerName ? " بواسطة {$ownerName}" : ''),
            meta: [
                'equipment_name' => $equipment->name,
                'owner_name' => $ownerName,
            ]
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */

    public static function newMessage(Conversation $conversation, User $receiver, User $sender, int $messageId): void
    {
        $senderName = self::userFullName($sender);

        $receiver->notify(new AppAlertNotification(
            kind: 'new_message',
            title: 'رسالة جديدة',
            message: "لديك رسالة جديدة" . ($senderName ? " من {$senderName}" : ''),
            meta: [
                'sender_name' => $senderName,
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
        $ownerName = self::userFullName($equipment->owner);

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

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $renterUrl = route('renter.payments.show', $booking->id);
        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم استلام الدفع',
            message: "تم استلام دفعة خاصة بحجز المعدة: {$equipment->name}",
            url: $renterUrl,
            meta: [
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

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم رفع وثيقة الدفع',
            message: "قام المستأجر برفع وثيقة دفع للمعدة: {$equipment->name}" . ($renterName ? " ({$renterName})" : ''),
            url: $ownerUrl,
            meta: [
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
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
            ]
        ));
    }

    public static function paymentApproved(Booking $booking, Equipment $equipment, float $amount): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::userFullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم قبول وثيقة الدفع',
            message: "تم قبول وثيقة الدفع الخاصة بالمعدة: {$equipment->name}",
            url: route('renter.payments.show', $booking->id),
            meta: [
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
        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'تم رفض وثيقة الدفع',
            message: "تم رفض وثيقة الدفع الخاصة بالمعدة: {$equipment->name}{$reasonText}",
            url: route('renter.payments.show', $booking->id),
            meta: [
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

        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $ownerUrl = route('owner.payments.show', $booking->id);
        $adminUrl = route('admin.bookings.show', $booking->id);

        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم اختيار الدفع النقدي',
            message: "اختار المستأجر الدفع النقدي للمعدة: {$equipment->name}" . ($renterName ? " ({$renterName})" : ''),
            url: $ownerUrl,
            meta: [
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
                'equipment_name' => $equipment->name,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'amount' => $booking->total_cost,
            ]
        ));
    }

    public static function paymentFailed(Booking $booking, Equipment $equipment, ?float $amount = null, ?string $reason = null): void
    {
        $equipment->loadMissing('owner');
        $booking->loadMissing('renter');

        $reasonText = $reason ? " (السبب: {$reason})" : "";
        $renterName = self::userFullName($booking->renter);
        $ownerName = self::userFullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'فشل الدفع',
            message: "فشلت عملية الدفع الخاصة بالمعدة: {$equipment->name}{$reasonText}",
            url: route('renter.payments.show', $booking->id),
            meta: [
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'فشل دفع',
            message: "فشلت عملية دفع خاصة بالمعدة: {$equipment->name}{$reasonText}",
            url: route('admin.bookings.show', $booking->id),
            meta: [
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'renter_name' => $renterName,
                'owner_name' => $ownerName,
                'reason' => $reason,
            ]
        ));
    }

    public static function refundIssued(Booking $booking, Equipment $equipment, float $amount): void
    {
        $equipment->loadMissing('owner');
        $ownerName = self::userFullName($equipment->owner);

        $booking->renter?->notify(new AppAlertNotification(
            kind: 'refund_issued',
            title: 'تم إصدار استرداد',
            message: "تم إصدار استرداد خاص بالمعدة: {$equipment->name}",
            url: route('renter.payments.show', $booking->id),
            meta: [
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'owner_name' => $ownerName,
            ]
        ));

        self::toAdmins(new AppAlertNotification(
            kind: 'refund_issued',
            title: 'تم إصدار استرداد',
            message: "تم إصدار استرداد خاص بالمعدة: {$equipment->name}",
            url: route('admin.bookings.show', $booking->id),
            meta: [
                'equipment_name' => $equipment->name,
                'amount' => $amount,
                'owner_name' => $ownerName,
            ]
        ));
    }

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
}

// namespace App\Services;

// use App\Models\User;
// use App\Models\Booking;
// use App\Models\Equipment;
// use App\Models\Conversation;
// use App\Notifications\AppAlertNotification;

// class NotificationService
// {
//     public static function admins()
//     {
//         return User::where('role', 'admin')->get();
//     }

//     public static function toAdmins(AppAlertNotification $notification): void
//     {
//         foreach (self::admins() as $admin) {
//             $admin->notify($notification);
//         }
//     }


//     // Booking


//     //  booking_request (owner + admins)
//     // إشعار للمؤجر + الأدمن عند وصول طلب حجز جديد

//     public static function bookingRequest(Booking $booking, Equipment $equipment): void
//     {
//         // owner
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'booking_request',
//             title: 'طلب حجز جديد',
//             message: "وصل طلب حجز جديد على المعدة: {$equipment->name} (حجز #{$booking->id})",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id' => $equipment->owner_id,
//                 'renter_id' => $booking->renter_id,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'booking_request',
//             title: 'طلب حجز جديد',
//             message: "تم إنشاء حجز جديد #{$booking->id} على {$equipment->name}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));
//     }

//     //  booking_confirmed (renter + admins)
//     // إشعار للمستأجر + الأدمن عند تأكيد الحجز

//     public static function bookingConfirmed(Booking $booking, Equipment $equipment): void
//     {
//         // renter
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'تم تأكيد الحجز',
//             message: "تم تأكيد الحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'تأكيد حجز',
//             message: "تم تأكيد الحجز #{$booking->id}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));
//     }

//     //  booking_cancelled (owner + renter + admins)
//     // إشعار للطرفين + الأدمن عند إلغاء الحجز
//     // الإلغاء عندك يغطي الرفض/الإلغاء معًا

//     public static function bookingCancelled(Booking $booking, Equipment $equipment, ?string $reason = null): void
//     {
//         $reasonText = $reason ? " (السبب: {$reason})" : "";

//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'تم إلغاء الحجز',
//             message: "تم إلغاء الحجز #{$booking->id} للمعدة: {$equipment->name}{$reasonText}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//                 'renter_id'    => $booking->renter_id,
//                 'reason'       => $reason,
//             ]
//         ));

//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'تم إلغاء حجز على معدتك',
//             message: "تم إلغاء الحجز #{$booking->id} على المعدة: {$equipment->name}{$reasonText}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//                 'renter_id'    => $booking->renter_id,
//                 'reason'       => $reason,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'إلغاء حجز',
//             message: "تم إلغاء الحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//                 'renter_id'    => $booking->renter_id,
//                 'reason'       => $reason,
//             ]
//         ));
//     }

//     // إشعار للطرفين عند انتهاء الحجز
//     public static function bookingCompleted(Booking $booking, Equipment $equipment): void
//     {
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'انتهى الحجز',
//             message: "انتهى الحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));

//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'تم إنهاء حجز على معدتك',
//             message: "تم إنهاء الحجز #{$booking->id} على المعدة: {$equipment->name}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'انتهاء حجز',
//             message: "انتهى الحجز #{$booking->id}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));
//     }

//     // إشعار للمستأجر عند اقتراب موعد الحجز
//     public static function bookingStartingSoon(Booking $booking, Equipment $equipment): void
//     {
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'اقتراب موعد الحجز',
//             message: "اقترب موعد الحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'start_date'   => $booking->start_date ?? null,
//             ]
//         ));
//     }


//     // عند إضافة معدة جديدة
//     public static function equipmentCreated(Equipment $equipment): void
//     {
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'system_alert',
//             title: 'تمت إضافة المعدة بنجاح',
//             message: "تمت إضافة المعدة: {$equipment->name} بنجاح وأصبحت ضمن معداتك.",
//             meta: [
//                 'equipment_id' => $equipment->id,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'system_alert',
//             title: 'تمت إضافة معدة جديدة',
//             message: "تمت إضافة معدة جديدة: {$equipment->name} (رقم #{$equipment->id})",
//             meta: [
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//             ]
//         ));
//     }

//     // Chat

//     //  new_message (receiver)
//     public static function newMessage(Conversation $conversation, User $receiver, User $sender, int $messageId): void
//     {
//         $receiver->notify(new AppAlertNotification(
//             kind: 'new_message',
//             title: 'رسالة جديدة',
//             message: "لديك رسالة جديدة من {$sender->first_name} {$sender->last_name}",
//             meta: [
//                 'conversation_id' => $conversation->id,
//                 'message_id' => $messageId,
//                 'sender_id' => $sender->id,
//             ]
//         ));
//     }


//     // GPS

//     //  equipment_moved (owner + admins)
//     public static function equipmentMoved(Equipment $equipment, float $distanceKm, float $lat, float $lng): void
//     {
//         // owner
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'equipment_moved',
//             title: 'تحذير حركة',
//             message: "تحركت المعدة {$equipment->name} لمسافة تقريباً " . number_format($distanceKm, 3) . " كم",
//             meta: [
//                 'equipment_id' => $equipment->id,
//                 'distance_km' => $distanceKm,
//                 'lat' => $lat,
//                 'lng' => $lng,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'equipment_moved',
//             title: 'تحذير حركة (GPS)',
//             message: "تحركت معدة #{$equipment->id} ({$equipment->name})",
//             meta: [
//                 'equipment_id' => $equipment->id,
//                 'distance_km' => $distanceKm,
//                 'lat' => $lat,
//                 'lng' => $lng,
//             ]
//         ));
//     }


//     // Payments


//     // payment_received (renter + owner + admins)
//     // تم استلام/اعتماد الدفعة

//     public static function paymentReceived(Booking $booking, Equipment $equipment, float $amount): void
//     {
//         // renter
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم استلام الدفع',
//             message: "تم استلام دفعة للحجز #{$booking->id} بقيمة {$amount}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//             ]
//         ));

//         // owner
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'دفعة جديدة',
//             message: "تم استلام دفعة للحجز #{$booking->id} على معدتك {$equipment->name} بقيمة {$amount}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم استلام دفعة',
//             message: "دفعة جديدة للحجز #{$booking->id} بقيمة {$amount}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//             ]
//         ));
//     }
//     // رفع وثيقة الدفع
//     public static function paymentProofSubmitted(Booking $booking, Equipment $equipment): void
//     {
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم رفع وثيقة الدفع',
//             message: "قام المستأجر برفع وثيقة دفع للحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'renter_id'    => $booking->renter_id,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم رفع وثيقة دفع',
//             message: "تم رفع وثيقة دفع للحجز #{$booking->id}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'renter_id'    => $booking->renter_id,
//             ]
//         ));
//     }

//     // قبول وثيقة الدفع
//     public static function paymentApproved(Booking $booking, Equipment $equipment, float $amount): void
//     {
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم قبول وثيقة الدفع',
//             message: "تم قبول وثيقة الدفع للحجز #{$booking->id} للمعدة: {$equipment->name} بقيمة {$amount}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount'       => $amount,
//             ]
//         ));
//     }

//     // رفض وثيقة الدفع
//     public static function paymentRejected(Booking $booking, Equipment $equipment, ?string $reason = null): void
//     {
//         $reasonText = $reason ? " (السبب: {$reason})" : "";

//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'تم رفض وثيقة الدفع',
//             message: "تم رفض وثيقة الدفع للحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'reason'       => $reason,
//             ]
//         ));
//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'تم رفض وثيقة دفع',
//             message: "تم رفض وثيقة الدفع للحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'reason'       => $reason,
//             ]
//         ));
//     }


//     // اختيار الدفع النقدي
//     public static function cashPaymentSelected(Booking $booking, Equipment $equipment): void
//     {
//         // owner
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم اختيار الدفع النقدي',
//             message: "اختار المستأجر الدفع النقدي للحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//                 'renter_id'    => $booking->renter_id,
//                 'amount'       => $booking->total_cost,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_received',
//             title: 'تم اختيار الدفع النقدي',
//             message: "تم اختيار الدفع النقدي للحجز #{$booking->id}",
//             meta: [
//                 'booking_id'   => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'owner_id'     => $equipment->owner_id,
//                 'renter_id'    => $booking->renter_id,
//                 'amount'       => $booking->total_cost,
//             ]
//         ));
//     }


//     // payment_failed (renter + admins)
//     public static function paymentFailed(Booking $booking, Equipment $equipment, ?float $amount = null, ?string $reason = null): void
//     {
//         $reasonText = $reason ? " (السبب: {$reason})" : "";

//         // renter
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'فشل الدفع',
//             message: "فشل الدفع للحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//                 'reason' => $reason,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'فشل دفع',
//             message: "فشل دفع للحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//                 'reason' => $reason,
//             ]
//         ));
//     }

//     // refund_issued (renter + admins)
//     public static function refundIssued(Booking $booking, Equipment $equipment, float $amount): void
//     {
//         // renter
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'refund_issued',
//             title: 'تم إصدار استرداد',
//             message: "تم إصدار استرداد للحجز #{$booking->id} بقيمة {$amount}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//             ]
//         ));

//         // admins
//         self::toAdmins(new AppAlertNotification(
//             kind: 'refund_issued',
//             title: 'تم إصدار استرداد',
//             message: "استرداد للحجز #{$booking->id} بقيمة {$amount}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'amount' => $amount,
//             ]
//         ));
//     }
//     // إشعار للمستأجر أن طلبه تم إرساله
//     public static function bookingCreatedByRenter(Booking $booking, Equipment $equipment): void
//     {
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_request',
//             title: 'تم إرسال طلب الحجز',
//             message: "تم إرسال طلب الحجز #{$booking->id} للمعدة: {$equipment->name} وهو الآن بانتظار موافقة المالك.",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));
//     }

//     public static function systemAlert(
//         User $user,
//         string $title,
//         string $message,
//         ?string $url = null,
//         array $meta = []
//     ): void {
//         $user->notify(new AppAlertNotification(
//             kind: 'system_alert',
//             title: $title,
//             message: $message,
//             url: $url,
//             meta: $meta
//         ));
//     }
// }