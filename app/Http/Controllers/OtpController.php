<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session OTP expired. Silakan login ulang']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'OTP sudah kadaluarsa. Silakan login ulang']);
        }


         $user->update(['otp' => null, 'otp_expires_at' => null]);

         Auth::login($user);
         $request->session()->regenerate();
         session()->forget('otp_user_id');

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

    }

