<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query  = $request->input('query');
    //     $status = $request->input('status');

    //     $bookings = Booking::with(['renter', 'equipment'])
    //         ->when($query, function ($q, $query) {
    //             $q->where('id', $query)
    //                 ->orWhereHas('renter', function ($sub) use ($query) {
    //                     $sub->where('first_name', 'like', "%{$query}%")
    //                         ->orWhere('last_name', 'like', "%{$query}%");
    //                 })
    //                 ->orWhereHas('equipment', function ($sub) use ($query) {
    //                     $sub->where('name', 'like', "%{$query}%");
    //                 });
    //         })
    //         ->when($status, function ($q, $status) {
    //             $q->where('booking_status', $status);
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('dashboard.bookings.index', compact('bookings', 'query', 'status'));
    // }
    public function index(Request $request)
    {
        $query  = $request->input('query');
        $status = $request->input('status');

        $bookings = Booking::with(['renter', 'equipment'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    // لو رقم، نخليه يطابق ID كمان
                    if (is_numeric($query)) {
                        $q->where('id', $query);
                    }

                    $q->orWhereHas('renter', function ($sub) use ($query) {
                        $sub->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%");
                    })
                        ->orWhereHas('equipment', function ($sub) use ($query) {
                            $sub->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when($status, function ($q, $status) {
                $q->where('booking_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // عشان يحافظ على الفلتر مع التنقل بين الصفحات

        return view('dashboard.bookings.index', compact('bookings', 'query', 'status'));
    }

    public function show(Booking $booking)
    {
        // نحمّل العلاقات عشان الفيو ما يعمل N+1
        $booking->load(['equipment', 'renter', 'owner']);

        return view('dashboard.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        if ($booking->booking_status === 'cancelled') {
            return back()->with('error', 'لا يمكن تأكيد حجز تم إلغاؤه.');
        }

        $booking->update([
            'booking_status'      => 'confirmed',
            'confirmed_at'        => now(),
            'cancelled_at'        => null,
            'cancellation_reason' => null,
        ]);

        return back()->with('success', 'تم تأكيد الحجز بنجاح.');
    }

    public function activate(Booking $booking)
    {
        if (! in_array($booking->booking_status, ['confirmed', 'pending'])) {
            return back()->with('error', 'لا يمكن تفعيل هذا الحجز من حالته الحالية.');
        }

        $booking->update([
            'booking_status' => 'active',
        ]);

        return back()->with('success', 'تم تفعيل الحجز (قيد التنفيذ).');
    }

    public function complete(Booking $booking)
    {
        if (! in_array($booking->booking_status, ['active', 'confirmed'])) {
            return back()->with('error', 'لا يمكن إنهاء حجز غير فعّال.');
        }

        $booking->update([
            'booking_status' => 'completed',
        ]);

        return back()->with('success', 'تم وضع الحجز كمنتهي.');
    }

    // "تعليق" = نرجعه لحالة pending / معلّق
    public function hold(Booking $booking)
    {
        if ($booking->booking_status === 'cancelled') {
            return back()->with('error', 'لا يمكن تعليق حجز ملغي.');
        }

        $booking->update([
            'booking_status' => 'pending',
            'confirmed_at'   => null,
        ]);

        return back()->with('success', 'تم تحويل الحجز إلى حالة معلّق.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($booking->booking_status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء حجز منتهي.');
        }

        $booking->update([
            'booking_status'      => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $data['reason'] ?? null,
        ]);

        return back()->with('success', 'تم إلغاء الحجز بنجاح.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete(); // Soft delete

        return back()->with('success', 'تم نقل الحجز إلى سلة المحذوفات.');
    }

    public function trash(Request $request)
    {
        $query = $request->input('query');

        $bookings = Booking::onlyTrashed()
            ->with(['renter', 'equipment'])
            ->when($query, function ($q, $query) {
                $q->where('id', $query);
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('dashboard.bookings.trash', compact('bookings', 'query'));
    }

    public function restore($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->restore();

        return back()->with('success', 'تم استرداد الحجز بنجاح.');
    }

    public function forceDelete($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->forceDelete();

        return back()->with('success', 'تم حذف الحجز نهائياً.');
    }

    public function restoreAll()
    {
        Booking::onlyTrashed()->restore();

        return back()->with('success', 'تم استرداد جميع الحجوزات المحذوفة.');
    }

    public function forceDeleteAll()
    {
        Booking::onlyTrashed()->forceDelete();

        return back()->with('success', 'تم حذف جميع الحجوزات نهائياً.');
    }
}
