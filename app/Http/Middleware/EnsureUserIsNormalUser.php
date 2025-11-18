<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNormalUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // لو المستخدم أدمن → رجّعيه على الداشبورد
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
