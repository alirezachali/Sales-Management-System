<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        if ($user = $request->user()) {
            Cache::forget('user-online-' . $user->id);
            $user->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
