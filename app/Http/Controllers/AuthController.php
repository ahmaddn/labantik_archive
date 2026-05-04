<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        // Cek apakah user ini terdaftar di ref_student
        $isRegistered = \DB::table('ref_student')
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isRegistered) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun ini tidak terdaftar sebagai siswa.',
            ])->onlyInput('email');
        }

        // Update last_login
        Auth::user()->update(['last_login' => now()]);

        return redirect()->intended(route('drive.index'));
    }

    return back()->withErrors([
        'email' => 'Email atau password tidak valid.',
    ])->onlyInput('email');
}
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
