<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
// بطلت تلزم
// use Illuminate\Validation\Rule;
use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
// لاستخدام قواعد التحقق الفريدة
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// تأكد من استيراد الـ User Model

class UserController extends Controller
{
    /**
     * عرض قائمة بجميع المستخدمين مع إمكانية البحث.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    // public function index(Request $request)
    // {
    //     $query  = $request->input('query');
    //     $status = $request->input('status', 'all'); // default = all
    //     $role   = $request->input('role', 'all');   // default = all

    //     $users = User::query()
    //         ->when($query, function ($q, $query) {
    //             $q->where('first_name', 'like', "%{$query}%")
    //                 ->orWhere('last_name', 'like', "%{$query}%")
    //                 ->orWhere('email', 'like', "%{$query}%");
    //         })->when($status && $status !== 'all', function ($q) use ($status) {
    //         $q->where('is_active', $status === 'active');
    //     })
    //         ->when($role && $role !== 'all', function ($q) use ($role) {
    //             $q->where('role', $role);
    //         })

    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10)       // عرض 10 مستخدمين في كل صفحة
    //         ->withQueryString(); // عشان يحافظ على الفلاتر مع الـ pagination

    //     return view('dashboard.users.index', compact('users', 'query', 'status', 'role'));
    // }
    public function index(Request $request)
    {
        $query  = $request->input('query');
        $status = $request->input('status', 'all'); // default = all
        $role   = $request->input('role', 'all');   // default = all

        $users = User::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status !== 'all', function ($q) use ($status) {
                // active → is_active = 1   |  inactive → is_active = 0
                $q->where('is_active', $status === 'active' ? 1 : 0 );
            })
            ->when($role !== 'all', function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // عشان يحافظ على الفلاتر في الصفحات

        return view('dashboard.users.index', compact('users', 'query', 'status', 'role'));
    }

    /**
     * تخزين مستخدم جديد.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name'  => ['required', 'string', 'max:255'],
    //         'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         // 'password'   => ['required', 'string', 'min:8','regex:/[a-z]/', 'regex:/[0-9]/'],
    //         'password'   => ['required', 'string', 'min:8'],
    //         'role'       => ['required', 'string', Rule::in(['user', 'admin'])],
    //     ]);

    //     User::create([
    //         'first_name' => $request->first_name,
    //         'last_name'  => $request->last_name,
    //         'email'      => $request->email,
    //         'password'   => Hash::make($request->password),
    //         'role'       => $request->role,
    //         'is_active'  => true, // المستخدمون الجدد يكونون نشطين افتراضياً
    //     ]);

    //     return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح.');
    // }
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
            'is_active'  => true,
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
    // public function update(Request $request, User $user)
    // {
    //     $request->validate([
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name'  => ['required', 'string', 'max:255'],
    //         'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    //         'password'   => ['nullable', 'string', 'min:8'], // يمكن تركها فارغة للتحديث
    //         'role'       => ['required', 'string', Rule::in(['user', 'admin'])],
    //     ]);

    //     $userData = [
    //         'first_name' => $request->first_name,
    //         'last_name'  => $request->last_name,
    //         'email'      => $request->email,
    //         'role'       => $request->role,
    //     ];

    //     // فقط قم بتحديث كلمة المرور إذا تم إدخال واحدة جديدة
    //     if ($request->filled('password')) {
    //         $userData['password'] = Hash::make($request->password);
    //     }

    //     $user->update($userData);

    //     return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    // }
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
// حماية إضافية: ممنوع تغيير دور الـ Super Admin (ID = 1)
        if ($user->id === 1 && $data['role'] !== 'admin') {
            return back()->with('error', 'لا يمكن تغيير صلاحيات المستخدم الأساسي (Super Admin).');
        }
        $userData = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
        ];

        if (! empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
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
        $stats = [
            'total_bookings'   => Booking::where('renter_id', $user->id)->count(),
            'cancelled'        => Booking::where('renter_id', $user->id)->where('booking_status', 'cancelled')->count(),
            'completed'        => Booking::where('renter_id', $user->id)->where('booking_status', 'completed')->count(),
            'total_complaints' => $complaints->count(),
        ];

        return view('dashboard.users.show', [
            'user'       => $user,
            'rentals'    => $rentals,
            'complaints' => $complaints,
            'stats'      => $stats,

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
