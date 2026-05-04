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

    $user = Auth::user();

    // Ambil role user ini
    $role = \DB::table('assoc_user_roles')
        ->join('core_roles', 'assoc_user_roles.role_id', '=', 'core_roles.id')
        ->where('assoc_user_roles.user_id', $user->id)
        ->value('core_roles.name'); // sesuaikan nama kolomnya

    // Kalau dia siswa, wajib ada di ref_student
    if ($role === 'siswa') {
        $isRegistered = \DB::table('ref_students')
            ->where('user_id', $user->id)
            ->exists();

        if (!$isRegistered) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun ini tidak terdaftar sebagai siswa aktif.',
            ])->onlyInput('email');
        }
    }

    // Superadmin, guru, dll langsung lolos
    $user->update(['last_login' => now()]);
    return redirect()->intended(route('drive.index'));
}
}
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
