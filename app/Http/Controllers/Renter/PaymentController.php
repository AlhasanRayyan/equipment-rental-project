<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Notifications\PaymentProofSubmitted;
// use App\Notifications\PaymentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Notification;
// use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    /**
     * عرض صفحة الدفع للحجز
     */
    public function show(Booking $booking)
    {
        // حماية: تأكد أن الحجز يخص هذا العميل
        if ($booking->renter_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['equipment.owner', 'payment', 'paymentProof']);

        return view('renter.payments.show', compact('booking'));
    }

    /**
     * اختيار طريقة الدفع (كاش أو تحويل)
     */
    public function selectMethod(Request $request, Booking $booking)
    {
        if ($booking->renter_id !== Auth::id()) abort(403);

        $request->validate([
            'payment_method_type' => 'required|in:cash,bank_transfer,wallet',
        ], [
            'payment_method_type.required' => 'يرجى اختيار طريقة الدفع.',
            'payment_method_type.in'       => 'طريقة الدفع غير صالحة.',
        ]);

        // إنشاء سجل الدفع أو تحديثه
        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id'             => Auth::id(),
                'amount'              => $booking->total_cost,
                'payment_method'      => in_array($request->payment_method_type, ['credit_card', 'paypal', 'bank_transfer', 'cash'])
                    ? $request->payment_method_type
                    : 'other',       // wallet → other
                'payment_method_type' => $request->payment_method_type,
                'status'              => 'pending',
                'payment_type'        => 'initial_payment',
            ]
        );

        // تحديث الحجز بطريقة الدفع المختارة
        $booking->update(['payment_method' => $request->payment_method_type]);

        if ($request->payment_method_type === 'cash') {
            // دفع كاش: أبلغ المالك أن العميل اختار الكاش
            // $this->notifyOwnerCashPayment($booking, $payment);

            $booking->load(['equipment.owner']);
            NotificationService::cashPaymentSelected($booking, $booking->equipment);

            return redirect()->route('renter.payments.show', $booking)
                ->with('success', 'تم اختيار طريقة الدفع النقدي. سيتم التواصل معك لترتيب عملية الاستلام.');
        }

        // تحويل بنكي/محفظة: اعرض نموذج رفع الإشعار
        return redirect()->route('renter.payments.upload-proof', $booking)->with('success', 'تم اختيار طريقة الدفع النقدي. سيتم التواصل معك لترتيب عملية الاستلام.');
    }

    /**
     * عرض نموذج رفع إشعار التحويل
     */
    public function showUploadProof(Booking $booking)
    {
        if ($booking->renter_id !== Auth::id()) abort(403);
        if (!in_array($booking->payment_method, ['bank_transfer', 'wallet'])) {
            return redirect()->route('renter.payments.show', $booking)
                ->with('error', 'طريقة الدفع المختارة لا تتطلب رفع إشعار.');
        }

        $booking->load(['equipment.owner', 'payment', 'paymentProof']);

        return view('renter.payments.upload-proof', compact('booking'));
    }

    /**
     * رفع إشعار التحويل البنكي/المحفظة
     */
    public function submitProof(Request $request, Booking $booking)
    {
        if ($booking->renter_id !== Auth::id()) abort(403);

        $request->validate([
            'transferred_amount'   => 'required|numeric|min:1',
            'bank_or_wallet_name'  => 'required|string|max:100',
            'proof_image'          => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'notes'                => 'nullable|string|max:500',
        ], [
            'transferred_amount.required'  => 'يرجى إدخال قيمة التحويل.',
            'transferred_amount.min'       => 'قيمة التحويل يجب أن تكون أكبر من صفر.',
            'bank_or_wallet_name.required' => 'يرجى إدخال اسم البنك أو المحفظة.',
            'proof_image.required'         => 'يرجى رفع صورة إشعار التحويل.',
            'proof_image.image'            => 'يجب أن يكون الملف صورة.',
            'proof_image.max'              => 'حجم الصورة يجب أن لا يتجاوز 5 ميغابايت.',
        ]);

        DB::transaction(function () use ($request, $booking) {
            // رفع صورة الإشعار
            $imagePath = $request->file('proof_image')
                ->store('payment-proofs/' . $booking->id, 'public');

            // جلب أو إنشاء سجل الدفع
            $payment = Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id'             => Auth::id(),
                    'amount'              => $booking->total_cost,
                    'payment_method'      => in_array($booking->payment_method, ['credit_card', 'paypal', 'bank_transfer', 'cash'])
                        ? $booking->payment_method
                        : 'other',
                    'payment_method_type' => $booking->payment_method,
                    'status'              => 'pending',
                    'payment_type'        => 'initial_payment',
                ]
            );

            // إنشاء أو تحديث سجل إثبات الدفع
            PaymentProof::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_id'          => $payment->id,
                    'renter_id'           => Auth::id(),
                    'transferred_amount'  => $request->transferred_amount,
                    'bank_or_wallet_name' => $request->bank_or_wallet_name,
                    'proof_image'         => $imagePath,
                    'notes'               => $request->notes,
                    'review_status'       => 'pending',
                ]
            );

            // تحديث حالة الحجز إلى "في انتظار التأكيد"
            $booking->update(['booking_status' => 'pending']);
        });

        // إشعار للمالك
        // $this->notifyOwnerProofSubmitted($booking);

        $booking->load(['equipment', 'renter', 'payment', 'paymentProof']);

        NotificationService::paymentProofSubmitted($booking, $booking->equipment);

        return redirect()->route('renter.payments.show', $booking)
            ->with('success', 'تم رفع إشعار التحويل بنجاح. سيتم مراجعته من قبل المالك قريباً.');
    }

    // ─── Private Helpers ─────────────────────────────────────────

    // private function notifyOwnerCashPayment(Booking $booking, Payment $payment): void
    // {
    //     $owner = $booking->equipment->owner;
    //     if ($owner) {
    //         $owner->notify(new PaymentProofSubmitted($booking, $payment, null, 'cash'));
    //     }
    // }

    // private function notifyOwnerProofSubmitted(Booking $booking): void
    // {
    //     $booking->load(['equipment.owner', 'paymentProof', 'payment']);
    //     $owner = $booking->equipment->owner;
    //     if ($owner) {
    //         $owner->notify(new PaymentProofSubmitted($booking, $booking->payment, $booking->paymentProof));
    //     }
    // }

    }
