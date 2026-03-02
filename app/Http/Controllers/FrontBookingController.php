<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AppAlertNotification;
use App\Models\User;
use App\Services\NotificationService;



class FrontBookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'rental_rate_type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'nullable|string',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        // 2. حساب السعر في السيرفر (للأمان، لا نعتمد على سعر الجافاسكربت)
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $days = $start->diff($end)->days;
        if ($days == 0) $days = 1;

        $rate = $equipment->getRateByType($request->rental_rate_type);
        $totalCost = $days * $rate;

        // 3. إنشاء الحجز
        $booking =  Booking::create([
            'equipment_id' => $equipment->id,
            'renter_id' => Auth::id(), // المستخدم الحالي
            'owner_id' => $equipment->owner_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'rental_duration_days' => $days,
            'rental_rate_type' => $request->rental_rate_type,
            'total_cost' => $totalCost,
            'deposit_amount_paid' => $equipment->deposit_amount,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'pickup_location' => $request->pickup_location,
        ]);

        NotificationService::bookingRequest($booking, $equipment);

        // $equipment->owner->notify(new AppAlertNotification(
        //     kind: 'booking_request',
        //     title: 'طلب حجز جديد',
        //     message: "وصل طلب حجز جديد للمعدة: {$equipment->name} (حجز #{$booking->id})",
        //     url: route('admin.bookings.show', $booking->id),
        //     meta: ['booking_id' => $booking->id, 'equipment_id' => $equipment->id]
        // ));

        // // للأدمن
        // User::where('role', 'admin')->get()->each(
        //     fn($admin) =>
        //     $admin->notify(new AppAlertNotification(
        //         kind: 'booking_request',
        //         title: 'حجز جديد في النظام',
        //         message: "تم إنشاء حجز جديد #{$booking->id} على {$equipment->name}",
        //         url: route('admin.bookings.show', $booking->id),
        //         meta: ['booking_id' => $booking->id]
        //     ))
        // );

        return back()->with('success', 'تم إرسال طلب الحجز بنجاح، بانتظار موافقة المالك.');
    }
}