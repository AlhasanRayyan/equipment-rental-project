<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        //  تحديث وقت آخر تسجيل دخول
        $user->update([
            'last_login_at' => now(),
        ]);

        // إشعار تسجيل دخول ناجح للمستخدم العادي فقط
        if ($user && $user->role !== 'admin') {
            NotificationService::systemAlert(
                $user,
                'تسجيل دخول ناجح',
                'تم تسجيل دخولك إلى حسابك بنجاح.',
                null,
                [
                    'login_at' => now()->toDateTimeString(),
                ]
            );
        }

        //  لو جاي من رابط فيه redirect (زي login?redirect=equipments.create)
        if ($request->has('redirect')) {
            return redirect()->route($request->redirect);
        }

        //  لو المستخدم أدمن → وديه على dashboard
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        //  غير هيك → نرجعه لصفحة intended (لو كان يزور صفحة محمية)
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
