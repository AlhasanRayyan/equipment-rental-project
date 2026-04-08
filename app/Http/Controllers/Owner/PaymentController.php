<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    /**
     * قائمة المدفوعات المعلقة للمالك
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $bookings = Booking::where('owner_id', Auth::id())
            ->with(['renter', 'equipment', 'payment', 'paymentProof'])
            ->whereNotNull('payment_method')
            ->when($status !== 'all', function ($q) use ($status) {
                // فلترة حسب حالة الدفع
                match ($status) {
                    'awaiting_cash'     => $q->where('payment_method', 'cash')
                        ->whereIn('booking_status', ['confirmed', 'pending']),
                    'proof_pending'     => $q->whereHas('paymentProof', fn($p) => $p->where('review_status', 'pending')),
                    'proof_approved'    => $q->whereHas('paymentProof', fn($p) => $p->where('review_status', 'approved')),
                    'proof_rejected'    => $q->whereHas('paymentProof', fn($p) => $p->where('review_status', 'rejected')),
                    default             => null,
                };
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('owner.payments.index', compact('bookings', 'status'));
    }

    /**
     * عرض تفاصيل الدفع لحجز معين
     */
    public function show(Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);

        $booking->load(['renter', 'equipment', 'payment', 'paymentProof.reviewer']);

        return view('owner.payments.show', compact('booking'));
    }

    /**
     * [كاش] تأكيد استلام الدفع النقدي → تحويل الحجز إلى "مدفوع"
     */
    public function confirmCashPayment(Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);
        if ($booking->payment_method !== 'cash') abort(400, 'هذا الحجز ليس بالدفع النقدي.');

        DB::transaction(function () use ($booking) {
            // تحديث حالة الدفع
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id'        => $booking->renter_id,
                    'amount'         => $booking->total_cost,
                    'payment_method' => 'cash',
                    'payment_type'   => 'initial_payment',
                    'status'         => 'completed',
                ]
            );

            // تحديث حالة الحجز
            $booking->update([
                'booking_status' => 'confirmed',
                'confirmed_at'   => now(),
            ]);
        });
        $booking->load(['equipment', 'renter']);

        NotificationService::paymentApproved($booking, $booking->equipment, (float) $booking->total_cost);
        NotificationService::paymentReceived($booking, $booking->equipment, (float) $booking->total_cost);
        NotificationService::bookingConfirmed($booking, $booking->equipment);
        // إشعار العميل
        // $booking->renter->notify(new PaymentStatusUpdated($booking, 'cash_confirmed'));

        return back()->with('success', 'تم تأكيد استلام المبلغ النقدي وتحديث حالة الحجز.');
    }

    /**
     * [تحويل] قبول إشعار التحويل البنكي/المحفظة
     */
    public function approveProof(Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);

        $proof = $booking->paymentProof;
        if (!$proof || !$proof->isPending()) {
            return back()->with('error', 'لا يوجد إشعار دفع معلق لهذا الحجز.');
        }

        DB::transaction(function () use ($booking, $proof) {
            // تحديث سجل إثبات الدفع
            $proof->update([
                'review_status' => 'approved',
                'reviewed_by'   => Auth::id(),
                'reviewed_at'   => now(),
            ]);

            $booking->payment->update([
                'status'       => 'completed',
                'payment_type' => 'initial_payment',
            ]);

            // تحديث حالة الحجز
            $booking->update([
                'booking_status' => 'confirmed',
                'confirmed_at'   => now(),
            ]);
        });

        // إشعار العميل
        // $booking->renter->notify(new PaymentStatusUpdated($booking, 'transfer_approved'));

        $booking->load(['equipment', 'renter']);

        NotificationService::paymentApproved($booking, $booking->equipment, (float) $booking->total_cost);
        NotificationService::paymentReceived($booking, $booking->equipment, (float) $booking->total_cost);
        NotificationService::bookingConfirmed($booking, $booking->equipment);

        return back()->with('success', 'تم قبول إشعار التحويل وتأكيد الحجز بنجاح.');
    }

    /**
     * [تحويل] رفض إشعار التحويل مع سبب
     */
    public function rejectProof(Request $request, Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'يرجى إدخال سبب الرفض.',
            'rejection_reason.min'      => 'سبب الرفض يجب أن يكون 10 أحرف على الأقل.',
        ]);

        $proof = $booking->paymentProof;
        if (!$proof || !$proof->isPending()) {
            return back()->with('error', 'لا يوجد إشعار دفع معلق لهذا الحجز.');
        }

        DB::transaction(function () use ($request, $booking, $proof) {
            // تحديث سجل إثبات الدفع
            $proof->update([
                'review_status'    => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
            ]);

            // إعادة حالة الدفع إلى معلق
            if ($booking->payment) {
                $booking->payment->update(['status' => 'failed']);
            }

            // إعادة حالة الحجز
            $booking->update(['booking_status' => 'pending']);
        });

        // إشعار العميل مع سبب الرفض
        // $booking->renter->notify(new PaymentStatusUpdated($booking, 'transfer_rejected', $request->rejection_reason));

        $booking->load(['equipment', 'renter']);
        NotificationService::paymentRejected($booking, $booking->equipment, $request->rejection_reason);

        return back()->with('success', 'تم رفض إشعار التحويل وإشعار العميل بالسبب.');
    }

    /**
     * [كاش] تحديث حالة الحجز النقدي (مالك يغير الحالة يدوياً)
     */
    public function updateCashStatus(Request $request, Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);
        if ($booking->payment_method !== 'cash') abort(400);

        $request->validate([
            'booking_status' => 'required|in:confirmed,active,completed,cancelled',
        ]);

        $updates = ['booking_status' => $request->booking_status];

        if ($request->booking_status === 'confirmed') {
            $updates['confirmed_at'] = now();
        } elseif ($request->booking_status === 'cancelled') {
            $updates['cancelled_at']        = now();
            $updates['cancellation_reason'] = $request->reason;
        }

        $booking->update($updates);

        // إشعار العميل
        // $booking->renter->notify(new PaymentStatusUpdated($booking, 'status_changed_' . $request->booking_status));

        $booking->load(['equipment', 'renter']);

        if ($request->booking_status === 'confirmed') {
            NotificationService::bookingConfirmed($booking, $booking->equipment);
        } elseif ($request->booking_status === 'cancelled') {
            NotificationService::bookingCancelled($booking, $booking->equipment, $request->reason ?? null);
        } elseif ($request->booking_status === 'completed') {
            NotificationService::bookingCompleted($booking, $booking->equipment);
        }

        return back()->with('success', 'تم تحديث حالة الحجز بنجاح.');
    }
}