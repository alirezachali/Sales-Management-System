<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'username'  => $request->username,
            'password'  => $request->password,
            'is_active' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            Auth::user()->update([
                'last_login_at' => now(),
            ]);

            /* هدایت کاربر بعد از ورود موفق به داشبورد متناسب با نقشش */
            return redirect()->intended(route(Auth::user()->dashboardRouteName()));
        }

        throw ValidationException::withMessages([
            'username' => 'نام کاربری یا رمز عبور اشتباه است.',
        ]);

    }
}