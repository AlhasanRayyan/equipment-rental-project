<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // أضف stateless() هنا
            $user = Socialite::driver('google')->stateless()->user();

            $finduser = User::where('google_id', $user->id)->orWhere('email', $user->email)->first();

            if ($finduser) {
                $finduser->update(['google_id' => $user->id]);
                Auth::login($finduser);
                return redirect('/');
            } else {
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : ' ';

                $newUser = User::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $user->email,
                    'google_id'  => $user->id,
                    'password'   => bcrypt(Str::random(16)),
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }
        } catch (Exception $e) {
            // إذا استمر الخطأ، اطبع الـ Exception بالكامل لتعرف نوع الكلاس الخاص به
            dd($e);
        }
    }
}
