<?php
namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    public function show()
    {
        // نسلك الحل حاليا
        // مؤقتًا: عرض أول مستخدم في قاعدة البيانات
        $user = User::first();

        if (! $user) {
            abort(404, 'لا يوجد مستخدم في قاعدة البيانات.');
        }
        // لما نعمل صفحة تسجيل دخول برجعها
        // $user = Auth::user(); // المستخدم الحالي
        // if (!$user) {
        //     abort(403, 'يجب تسجيل الدخول لعرض الصفحة.');
        // }

        return view('frontend.user.profile', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
                // نسلك الحل حاليا
        // مؤقتًا: عرض أول مستخدم في قاعدة البيانات
        $user = User::first();

        if (! $user) {
            abort(404, 'لا يوجد مستخدم في قاعدة البيانات.');
        }
        // لما نعمل صفحة تسجيل دخول برجعها

        // $user = Auth::user();
        return view('frontend.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        // التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:255',
            'bio'           => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // حفظ الصورة الجديدة إذا تم رفعها
        if ($request->hasFile('profile_image')) {
            // حذف القديمة إن وجدت
            if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
                Storage::delete('public/' . $user->profile_image);
            }

            $path                           = $request->file('profile_image')->store('users', 'public');
            $validatedData['profile_image'] = $path;
        }

        // تحديث البيانات
        $user->update($validatedData);

        return redirect()->route('profile.show')->with('success', 'تم تحديث بياناتك بنجاح');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
