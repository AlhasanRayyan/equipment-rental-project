<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        // جلب فقط الحجوزات الخاصة بمعدات هذا المالك
        $bookings = Booking::where('owner_id', Auth::id())
            ->with(['renter', 'equipment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // حماية: التأكد أن الحجز يخص هذا المالك فعلاً
        if ($booking->owner_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز.');
        }

        return view('owner.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);

        $booking->update([
            'booking_status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        return back()->with('success', 'تم تأكيد الحجز بنجاح.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->owner_id !== Auth::id()) abort(403);

        $booking->update([
            'booking_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason
        ]);

        return back()->with('success', 'تم إلغاء الحجز.');
    }
}