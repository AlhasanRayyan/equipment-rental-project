<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $bookingId,
        public string $equipmentName
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'booking_cancelled',
            'data' => "تم إلغاء الحجز #{$this->bookingId} للمعدة: {$this->equipmentName}",
            'url'  => route('admin.bookings.show', $this->bookingId),
            'booking_id' => $this->bookingId,
        ];
    }
}