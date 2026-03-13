<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $bookingId,
        public string $equipmentName
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // لاحقاً ضيفي 'mail'
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'booking_request',
            'data' => "وصلَك طلب حجز جديد للمعدة: {$this->equipmentName} (حجز #{$this->bookingId})",
            'url'  => route('admin.bookings.show', $this->bookingId),
            'booking_id' => $this->bookingId,
        ];
    }
}
