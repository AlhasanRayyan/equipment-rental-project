<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // public function show()
    // {
    //     // نسلك الحل حاليا
    //     // مؤقتًا: عرض أول مستخدم في قاعدة البيانات
    //     $user = User::first();

    //     if (! $user) {
    //         abort(404, 'لا يوجد مستخدم في قاعدة البيانات.');
    //     }
    //     // لما نعمل صفحة تسجيل دخول برجعها
    //     // $user = Auth::user(); // المستخدم الحالي
    //     // if (!$user) {
    //     //     abort(403, 'يجب تسجيل الدخول لعرض الصفحة.');
    //     // }

    //     return view('frontend.user.profile', compact('user'));
    // }
    public function show()
    {
        $user = Auth::user();

        // لو مش داخل (بس بشكل مؤقت بدون نظام مصادقة)
        if (! $user) {
            return view('frontend.user.profile')->with([
                'equipmentsCount' => 0,
                'bookingsCount'   => 0,
                'favoritesCount'  => 0,
                'invoicesCount'   => 0,
                'rentedCount'     => 0,
                'user'            => null,
            ]);
        }

        // إحصائيات المستخدم
        $equipmentsCount = $user->equipments()->count();
        $bookingsCount   = $user->bookings()->count();
        $favoritesCount  = $user->favorites()->count();
        $invoicesCount   = $user->invoices()->count();
        $rentedCount     = Booking::where('renter_id', $user->id)->count();

        return view('frontend.user.profile', compact(
            'user',
            'equipmentsCount',
            'bookingsCount',
            'favoritesCount',
            'invoicesCount',
            'rentedCount'
        ));
    }

    public function loadSection($type)
    {
        // إذا المستخدم مش مسجّل دخول
        if (! Auth::check()) {
            // نرجّع رسالة HTML بسيطة بدل Error
            return response('<p class="uk-text-center uk-text-muted">الرجاء تسجيل الدخول لعرض هذه المعلومات.</p>');
        }
        $user = Auth::user();

        switch ($type) {
            case 'equipments':
                $equipments = $user->equipments;
                return view('frontend.user.sections.equipments', compact('equipments'));

            case 'bookings':
                $bookings = $user->bookings()->with('equipment')->get();
                return view('frontend.user.sections.bookings', compact('bookings'));

            case 'favorites':
                $favorites = $user->favorites()->with('equipment')->get();
                return view('frontend.user.sections.favorites', compact('favorites'));

            case 'invoices':
                $invoices = $user->invoices;
                return view('frontend.user.sections.invoices', compact('invoices'));

            case 'rented':
                // حجوزات كمستأجر
                $rented = Booking::where('user_id', $user->id)->with('equipment')->get();
                return view('frontend.user.sections.rented', compact('rented'));

            default:
                return response('القسم غير موجود', 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        // نسلك الحل حاليا
        // مؤقتًا: عرض أول مستخدم في قاعدة البيانات
        // $user = User::first();
    $user = Auth::user();

        if (! $user) {
            // abort(404, 'لا يوجد مستخدم في قاعدة البيانات.');
            return redirect()->route('home')->with('error', 'يجب تسجيل الدخول لتعديل الملف الشخصي.');

        }
        // لما نعمل صفحة تسجيل دخول برجعها

        // $user = Auth::user();
        return view('frontend.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('home')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        // التحقق من صحة البيانات المدخلة

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone_number'         => 'nullable|string|max:20',
            'location_text'       => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // حفظ الصورة الجديدة إذا تم رفعها

        if ($request->hasFile('profile_image')) {
            // حذف القديمة إن وجدت

            if ($user->profile_picture_url && Storage::exists('public/' . $user->profile_picture_url)) {
                Storage::delete('public/' . $user->profile_picture_url);
            }

            $path                      = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_picture_url = $path; //  نفس اسم العمود
        }

        // تحديث البيانات
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->phone_number      = $request->phone_number;
        $user->location_text    = $request->location_text;
        $user->description        = $request->description;
        $user->save();
        return redirect()->route('profile.show', $user->id)->with('success', 'تم تحديث بياناتك بنجاح');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
