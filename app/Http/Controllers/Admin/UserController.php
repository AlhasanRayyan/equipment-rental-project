<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // تأكد من استيراد الـ User Model
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // لاستخدام قواعد التحقق الفريدة

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
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['user', 'admin'])],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true, // المستخدمون الجدد يكونون نشطين افتراضياً
        ]);

        return redirect()->route('admin.users.store')->with('success', 'تم إضافة المستخدم بنجاح.');
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
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'], // يمكن تركها فارغة للتحديث
            'role' => ['required', 'string', Rule::in(['user', 'admin'])],
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
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
}