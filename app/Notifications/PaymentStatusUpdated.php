<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * إشعار للعميل عند تغيير حالة دفعه
 */
class PaymentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string  $action,           // cash_confirmed | transfer_approved | transfer_rejected | status_changed_*
        public ?string $rejectionReason = null
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting('مرحباً ' . $notifiable->name);

        return match (true) {
            $this->action === 'cash_confirmed' => $mail
                ->subject('تأكيد الحجز - ' . $this->booking->equipment->name)
                ->line('تم تأكيد استلام دفعتك النقدية للحجز رقم #' . $this->booking->id)
                ->line('المعدة: ' . $this->booking->equipment->name)
                ->line('شكراً لاستخدامك منصتنا!'),

            $this->action === 'transfer_approved' => $mail
                ->subject('تم قبول إشعار التحويل - حجز #' . $this->booking->id)
                ->line('تمت مراجعة إشعار التحويل الخاص بك وقبوله.')
                ->line('الحجز مؤكد الآن. تواصل مع المالك لترتيب الاستلام.'),

            $this->action === 'transfer_rejected' => $mail
                ->subject('تم رفض إشعار التحويل - حجز #' . $this->booking->id)
                ->line('للأسف، تم رفض إشعار التحويل الخاص بك.')
                ->line('السبب: ' . ($this->rejectionReason ?? 'لم يتم تحديد السبب.'))
                ->action('رفع إشعار جديد', route('renter.payments.upload-proof', $this->booking))
                ->line('يرجى التواصل مع المالك أو رفع إشعار صحيح.'),

            default => $mail
                ->subject('تحديث على حجزك #' . $this->booking->id)
                ->line('تم تحديث حالة حجزك.'),
        };
    }

    public function toArray($notifiable): array
    {
        return [
            'type'             => 'payment_status_updated',
            'booking_id'       => $this->booking->id,
            'action'           => $this->action,
            'rejection_reason' => $this->rejectionReason,
            'message'          => $this->getMessage(),
        ];
    }

    private function getMessage(): string
    {
        return match (true) {
            $this->action === 'cash_confirmed'    => 'تم تأكيد استلام دفعتك النقدية للحجز #' . $this->booking->id,
            $this->action === 'transfer_approved' => 'تم قبول إشعار التحويل وتأكيد حجزك #' . $this->booking->id,
            $this->action === 'transfer_rejected' => 'تم رفض إشعار التحويل للحجز #' . $this->booking->id,
            default                               => 'تم تحديث حالة الحجز #' . $this->booking->id,
        };
    }
}