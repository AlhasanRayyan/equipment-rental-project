<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // تأكد من استيراد الـ User Model
use Illuminate\Validation\Rule;
// لاستخدام قواعد التحقق الفريدة

class UserController extends Controller
{
    /**
     * عرض قائمة بجميع المستخدمين مع إمكانية البحث.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        $users = User::query()
            ->when($query, function ($q, $query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // عرض 10 مستخدمين في كل صفحة

        return view('dashboard.users.index', compact('users', 'query'));
    }

    /**
     * تخزين مستخدم جديد.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8'],
            'role'       => ['required', 'string', Rule::in(['user', 'admin'])],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => true, // المستخدمون الجدد يكونون نشطين افتراضياً
        ]);

        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    /**
     * تحديث معلومات مستخدم موجود.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'   => ['nullable', 'string', 'min:8'], // يمكن تركها فارغة للتحديث
            'role'       => ['required', 'string', Rule::in(['user', 'admin'])],
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'role'       => $request->role,
        ];

        // فقط قم بتحديث كلمة المرور إذا تم إدخال واحدة جديدة
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    /**
     * تفعيل حساب مستخدم.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(User $user)
    {
        if ($user->id === 1 || auth()->id() === $user->id) { // منع تعطيل المستخدم الأول أو الحالي
            return redirect()->route('admin.users.index')->with('error', 'لا يمكنك تفعيل هذا المستخدم.');
        }

        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')->with('success', 'تم تفعيل المستخدم بنجاح.');
    }

    /**
     * تعطيل حساب مستخدم.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Request $request, User $user)
    {
        if ($user->id === 1 || auth()->id() === $user->id) { // منع تعطيل المستخدم الأول أو الحالي
            return redirect()->route('admin.users.index')->with('error', 'لا يمكنك تعطيل هذا المستخدم.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('success', 'تم تعطيل المستخدم بنجاح.');
    }

    // عرض تفاصيل مستخدم ما
    public function show(User $user)
    {
        // جلب الحجوزات المرتبطة بالمستخدم (كمستأجر)
        $rentals = Booking::where('renter_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // جلب الشكاوى / الاستفسارات اللي هو أرسلها
        $complaints = Message::where('sender_id', $user->id)
            ->whereIn('message_type', ['complaint', 'inquiry'])
            ->latest()
            ->get();

        return view('dashboard.users.show', [
            'user'       => $user,
            'rentals'    => $rentals,
            'complaints' => $complaints,
        ]);
    }
    /**
     * حذف مستخدم.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        if ($user->id === 1 || auth()->id() === $user->id) { // منع حذف المستخدم الأول أو الحالي
            return redirect()->route('admin.users.index')->with('error', 'لا يمكنك حذف هذا المستخدم.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }
    /**
     * عرض المستخدمين الموجودين في سلة المحذوفات.
     */
    public function trash(Request $request)
    {
        $query = $request->input('query');

        $users = User::onlyTrashed()
            ->when($query, function ($q, $query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('dashboard.users.trash', compact('users', 'query'));
    }

    /**
     * استرجاع مستخدم واحد من سلة المحذوفات.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        return redirect()->route('admin.users.trash')->with('success', 'تم استعادة المستخدم بنجاح.');
    }

    /**
     * استرجاع كل المستخدمين من سلة المحذوفات.
     */
    public function restoreAll()
    {
        User::onlyTrashed()->restore();

        return redirect()->route('admin.users.trash')->with('success', 'تم استعادة جميع المستخدمين المحذوفين.');
    }

    /**
     * حذف نهائي لمستخدم واحد مع مراعاة العلاقات.
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // 🧷 مراعاة العلاقات: لو عنده حجوزات/معدات/رسائل، امنعي الحذف النهائي
        if (
            $user->bookings()->exists() ||
            $user->equipments()->exists() ||
            $user->sentMessages()->exists()
        ) {
            return back()->with('error', 'لا يمكن حذف المستخدم نهائياً لأنه مرتبط بحجوزات أو معدات أو شكاوى.');
        }

        $user->forceDelete();

        return redirect()->route('admin.users.trash')->with('success', 'تم حذف المستخدم نهائياً.');
    }

    /**
     * حذف نهائي لجميع المستخدمين الموجودين في سلة المحذوفات (اللي ما إلهم علاقات).
     */
    public function forceDeleteAll()
    {
        $users = User::onlyTrashed()->get();

        $deletedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                if (
                    $user->bookings()->exists() ||
                    $user->equipments()->exists() ||
                    $user->sentMessages()->exists()
                ) {
                    continue; // اتركيه، لسه مرتبط
                }

                $user->forceDelete();
                $deletedCount++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي.');
        }

        return back()->with('success', "تم حذف {$deletedCount} مستخدم نهائياً (تخطي المستخدمين المرتبطين ببيانات أخرى).");
    }
}
