<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            // استخدمنا stateless لتجنب المشاكل التي واجهتها سابقاً
            $user = Socialite::driver('facebook')->stateless()->user();
            
            $finduser = User::where('facebook_id', $user->id)->orWhere('email', $user->email)->first();

            if ($finduser) {
                $finduser->update(['facebook_id' => $user->id]);
                Auth::login($finduser);
                return redirect()->intended('dashboard');
            } else {
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : ' ';

                $newUser = User::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $user->email,
                    'facebook_id'=> $user->id,
                    'password'   => bcrypt(Str::random(16)),
                ]);

                Auth::login($newUser);
                return redirect()->intended('dashboard');
            }
        } catch (Exception $e) {
            return redirect('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول بفيسبوك');
        }
    }
}