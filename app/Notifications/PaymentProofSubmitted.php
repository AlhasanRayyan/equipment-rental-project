<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * إشعار للمالك عند رفع العميل إشعار دفع جديد
 */
class PaymentProofSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking      $booking,
        public Payment      $payment,
        public ?PaymentProof $proof = null,
        public string       $paymentType = 'transfer' // cash | transfer
    ) {}

    public function via($notifiable): array
    {
        // المالك يشوف الإشعار في الداشبورد فقط، بدون إيميل
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'         => 'payment_proof_submitted',
            'booking_id'   => $this->booking->id,
            'renter_name'  => $this->booking->renter->name,
            'amount'       => $this->booking->total_cost,
            'payment_type' => $this->paymentType,
            'url'          => route('owner.payments.show', $this->booking),
            'message'      => $this->paymentType === 'cash'
                ? 'اختار العميل ' . $this->booking->renter->name . ' الدفع النقدي للحجز #' . $this->booking->id
                : 'رفع العميل ' . $this->booking->renter->name . ' إشعار تحويل للحجز #' . $this->booking->id . ' - بانتظار مراجعتك',
        ];
    }
}