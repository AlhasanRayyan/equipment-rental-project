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

    public static function toAdmins(AppAlertNotification $n): void
    {
        foreach (self::admins() as $admin) {
            $admin->notify($n);
        }
    }

    // =========================
    // Booking
    // =========================

    // 1) booking_request (owner + admins)
    public static function bookingRequest(Booking $booking, Equipment $equipment): void
    {
        // owner
        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_request',
            title: 'طلب حجز جديد',
            message: "وصل طلب حجز جديد على المعدة: {$equipment->name} (حجز #{$booking->id})",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'owner_id' => $equipment->owner_id,
                'renter_id' => $booking->renter_id,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'booking_request',
            title: 'طلب حجز جديد',
            message: "تم إنشاء حجز جديد #{$booking->id} على {$equipment->name}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
            ]
        ));
    }

    // 2) booking_confirmed (renter + admins)
    public static function bookingConfirmed(Booking $booking, Equipment $equipment): void
    {
        // renter
        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تم تأكيد الحجز',
            message: "تم تأكيد الحجز #{$booking->id} للمعدة: {$equipment->name}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'booking_confirmed',
            title: 'تأكيد حجز',
            message: "تم تأكيد الحجز #{$booking->id}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
            ]
        ));
    }

    // 3) booking_cancelled (owner + renter + admins)
    public static function bookingCancelled(Booking $booking, Equipment $equipment, ?string $reason = null): void
    {
        $reasonText = $reason ? " (السبب: {$reason})" : "";

        // renter
        $booking->renter?->notify(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'تم إلغاء الحجز',
            message: "تم إلغاء الحجز #{$booking->id} للمعدة: {$equipment->name}{$reasonText}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'reason' => $reason,
            ]
        ));

        // owner
        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'تم إلغاء حجز على معدتك',
            message: "تم إلغاء الحجز #{$booking->id} على المعدة: {$equipment->name}{$reasonText}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'reason' => $reason,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'booking_cancelled',
            title: 'إلغاء حجز',
            message: "تم إلغاء الحجز #{$booking->id}{$reasonText}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'reason' => $reason,
            ]
        ));
    }

    // =========================
    // Chat
    // =========================
    // 4) new_message (receiver)
    public static function newMessage(Conversation $conversation, User $receiver, User $sender, int $messageId): void
    {
        $receiver->notify(new AppAlertNotification(
            kind: 'new_message',
            title: 'رسالة جديدة',
            message: "لديك رسالة جديدة من {$sender->first_name} {$sender->last_name}",
            meta: [
                'conversation_id' => $conversation->id,
                'message_id' => $messageId,
                'sender_id' => $sender->id,
            ]
        ));
    }

    // =========================
    // GPS
    // =========================
    // 5) equipment_moved (owner + admins)
    public static function equipmentMoved(Equipment $equipment, float $distanceKm, float $lat, float $lng, float $speed): void
    {
        // owner
        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'equipment_moved',
            title: 'تحذير حركة',
            message: "تحركت المعدة {$equipment->name} لمسافة تقريباً " . number_format($distanceKm, 3) . " كم",
            meta: [
                'equipment_id' => $equipment->id,
                'distance_km' => $distanceKm,
                'lat' => $lat,
                'lng' => $lng,
                'speed' => $speed,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'equipment_moved',
            title: 'تحذير حركة (GPS)',
            message: "تحركت معدة #{$equipment->id} ({$equipment->name})",
            meta: [
                'equipment_id' => $equipment->id,
                'distance_km' => $distanceKm,
                'lat' => $lat,
                'lng' => $lng,
                'speed' => $speed,
            ]
        ));
    }

    // =========================
    // Payments
    // =========================

    // payment_received (renter + owner + admins)
    public static function paymentReceived(Booking $booking, Equipment $equipment, float $amount): void
    {
        // renter
        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم استلام الدفع',
            message: "تم استلام دفعة للحجز #{$booking->id} بقيمة {$amount}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
            ]
        ));

        // owner
        $equipment->owner?->notify(new AppAlertNotification(
            kind: 'payment_received',
            title: 'دفعة جديدة',
            message: "تم استلام دفعة للحجز #{$booking->id} على معدتك {$equipment->name} بقيمة {$amount}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'payment_received',
            title: 'تم استلام دفعة',
            message: "دفعة جديدة للحجز #{$booking->id} بقيمة {$amount}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
            ]
        ));
    }

    // payment_failed (renter + admins)
    public static function paymentFailed(Booking $booking, Equipment $equipment, ?float $amount = null, ?string $reason = null): void
    {
        $reasonText = $reason ? " (السبب: {$reason})" : "";

        // renter
        $booking->renter?->notify(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'فشل الدفع',
            message: "فشل الدفع للحجز #{$booking->id}{$reasonText}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
                'reason' => $reason,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'payment_failed',
            title: 'فشل دفع',
            message: "فشل دفع للحجز #{$booking->id}{$reasonText}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
                'reason' => $reason,
            ]
        ));
    }

    // refund_issued (renter + admins)
    public static function refundIssued(Booking $booking, Equipment $equipment, float $amount): void
    {
        // renter
        $booking->renter?->notify(new AppAlertNotification(
            kind: 'refund_issued',
            title: 'تم إصدار استرداد',
            message: "تم إصدار استرداد للحجز #{$booking->id} بقيمة {$amount}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
            ]
        ));

        // admins
        self::toAdmins(new AppAlertNotification(
            kind: 'refund_issued',
            title: 'تم إصدار استرداد',
            message: "استرداد للحجز #{$booking->id} بقيمة {$amount}",
            meta: [
                'booking_id' => $booking->id,
                'equipment_id' => $equipment->id,
                'amount' => $amount,
            ]
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

//     public static function toAdmins(AppAlertNotification $n): void
//     {
//         foreach (self::admins() as $admin) {
//             $admin->notify($n);
//         }
//     }

//     //  1) طلب حجز جديد (للمالك + الأدمن)
//     public static function bookingRequest(Booking $booking, Equipment $equipment): void
//     {
//         // للمالك
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

//         // للأدمن
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

//     //  2) تأكيد حجز (للمستأجر + الأدمن)
//     public static function bookingConfirmed(Booking $booking, Equipment $equipment): void
//     {
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_confirmed',
//             title: 'تم تأكيد الحجز',
//             message: "تم تأكيد الحجز #{$booking->id} للمعدة: {$equipment->name}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//             ]
//         ));

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

//     //  3) إلغاء حجز (للمستأجر + المالك + الأدمن)
//     public static function bookingCancelled(Booking $booking, Equipment $equipment, ?string $reason = null): void
//     {
//         $reasonText = $reason ? " (السبب: {$reason})" : "";

//         // للمستأجر
//         $booking->renter?->notify(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'تم إلغاء الحجز',
//             message: "تم إلغاء الحجز #{$booking->id} للمعدة: {$equipment->name}{$reasonText}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'reason' => $reason,
//             ]
//         ));

//         // للمالك
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'تم إلغاء حجز على معدتك',
//             message: "تم إلغاء الحجز #{$booking->id} على المعدة: {$equipment->name}{$reasonText}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'reason' => $reason,
//             ]
//         ));

//         // للأدمن
//         self::toAdmins(new AppAlertNotification(
//             kind: 'booking_cancelled',
//             title: 'إلغاء حجز',
//             message: "تم إلغاء الحجز #{$booking->id}{$reasonText}",
//             meta: [
//                 'booking_id' => $booking->id,
//                 'equipment_id' => $equipment->id,
//                 'reason' => $reason,
//             ]
//         ));
//     }

//     //  4) رسالة جديدة
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

//     //  5) GPS تحركت معدة (للمالك + الأدمن)
//     public static function equipmentMoved(Equipment $equipment, float $distanceKm, float $lat, float $lng, float $speed): void
//     {
//         $equipment->owner?->notify(new AppAlertNotification(
//             kind: 'equipment_moved',
//             title: 'تحذير حركة',
//             message: "تحركت المعدة {$equipment->name} لمسافة تقريباً " . number_format($distanceKm, 3) . " كم",
//             meta: [
//                 'equipment_id' => $equipment->id,
//                 'distance_km' => $distanceKm,
//                 'lat' => $lat,
//                 'lng' => $lng,
//                 'speed' => $speed,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'equipment_moved',
//             title: 'تحذير حركة (GPS)',
//             message: "تحركت معدة #{$equipment->id} ({$equipment->name})",
//             meta: [
//                 'equipment_id' => $equipment->id,
//                 'distance_km' => $distanceKm,
//                 'lat' => $lat,
//                 'lng' => $lng,
//                 'speed' => $speed,
//             ]
//         ));
//     }

//     //  6) الدفع (لو عندك منطق دفع)
//     public static function paymentFailed(User $user, ?int $bookingId = null, ?float $amount = null, ?string $reason = null): void
//     {
//         $user->notify(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'فشل الدفع',
//             message: $reason ? "فشل الدفع: {$reason}" : "فشل الدفع، حاول مرة أخرى.",
//             meta: [
//                 'booking_id' => $bookingId,
//                 'amount' => $amount,
//                 'reason' => $reason,
//             ]
//         ));

//         self::toAdmins(new AppAlertNotification(
//             kind: 'payment_failed',
//             title: 'فشل دفع',
//             message: 'حدث فشل دفع' . ($bookingId ? " (حجز #{$bookingId})" : ''),
//             meta: [
//                 'booking_id' => $bookingId,
//                 'amount' => $amount,
//                 'reason' => $reason,
//                 'user_id' => $user->id,
//             ]
//         ));
//     }
// }