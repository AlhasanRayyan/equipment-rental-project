<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'                 => 'required|exists:bookings,id',
            'rating_overall'             => 'required|integer|min:1|max:5',
            'comment'                    => 'nullable|string|max:1000',
            'owner_communication_rating' => 'nullable|integer|min:1|max:5',
            'equipment_condition_rating' => 'nullable|integer|min:1|max:5',
            'renter_punctuality_rating'  => 'nullable|integer|min:1|max:5',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // تأكد إن الحجز منتهي
        if ($booking->booking_status !== 'completed') {
            return back()->with('error', 'لا يمكن التقييم إلا بعد انتهاء الحجز.');
        }

        // تأكد إن المستخدم طرف في الحجز
        if (auth()->id() !== $booking->renter_id && auth()->id() !== $booking->owner_id) {
            abort(403);
        }

        // تأكد ما قيّم قبل
        $exists = Review::where('booking_id', $booking->id)
            ->where('reviewer_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'لقد قمت بتقييم هذا الحجز مسبقاً.');
        }

        // المُقيَّم هو الطرف الآخر
        $reviewedUserId = auth()->id() === $booking->renter_id
            ? $booking->owner_id
            : $booking->renter_id;

        Review::create([
            'booking_id'                 => $booking->id,
            'reviewer_id'                => auth()->id(),
            'reviewed_user_id'           => $reviewedUserId,
            'equipment_id'               => $booking->equipment_id,
            'rating_overall'             => $request->rating_overall,
            'comment'                    => $request->comment,
            'owner_communication_rating' => $request->owner_communication_rating,
            'equipment_condition_rating' => $request->equipment_condition_rating,
            'renter_punctuality_rating'  => $request->renter_punctuality_rating,
        ]);

        // تحديث متوسط التقييم في المعدة
        $avg = Review::where('equipment_id', $booking->equipment_id)->avg('rating_overall');
        $booking->equipment->update(['average_rating' => $avg]);

        return back()->with('success', 'تم إرسال تقييمك بنجاح!');
    }
}