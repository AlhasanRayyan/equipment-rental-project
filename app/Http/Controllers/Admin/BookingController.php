<?php

namespace App\Http\Controllers\Admin; // تأكد من هذا السطر

use App\Http\Controllers\Controller;
use App\Models\Booking; // استدعاء الموديل
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query  = $request->input('query');
        $status = $request->input('status');

        $bookings = Booking::with(['renter', 'equipment'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
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
            ->withQueryString();

        return view('dashboard.bookings.index', compact('bookings', 'query', 'status'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['equipment', 'renter', 'owner']);
        return view('dashboard.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        $booking->update([
            'booking_status' => 'confirmed',
            'confirmed_at'   => now(),
        ]);
        return back()->with('success', 'تم تأكيد الحجز.');
    }

    // ... باقي الدوال (activate, complete, cancel) تأكد أنها موجودة هنا

    public function cancel(Request $request, Booking $booking)
    {
        $booking->update([
            'booking_status' => 'cancelled',
            'cancelled_at'   => now(),
            'cancellation_reason' => $request->reason
        ]);
        return back()->with('success', 'تم إلغاء الحجز.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'تم الحذف.');
    }
}
