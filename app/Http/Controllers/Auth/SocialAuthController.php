<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /* ===========================
     | Google Authentication
     |===========================*/

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(12)),
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                ]);
            } else {
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleUser->id,
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/');

        } catch (Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['social' => 'Google login failed. Please try again.']);
        }
    }

    /* ===========================
     | Facebook Authentication
     |===========================*/

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $fbUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $fbUser->name,
                    'email' => $fbUser->email,
                    'password' => Hash::make(Str::random(12)),
                    'provider' => 'facebook',
                    'provider_id' => $fbUser->id,
                ]);
            } else {
                $user->update([
                    'provider' => 'facebook',
                    'provider_id' => $fbUser->id,
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/');

        } catch (Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['social' => 'Facebook login failed. Please try again.']);
        }
    }
}
