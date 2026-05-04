<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // Cari semua user dengan email ini
        $users = User::where('email', $credentials['email'])->get();

        if ($users->isEmpty()) {
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
        }

        // Filter yang passwordnya cocok
        $matched = $users->filter(fn($u) => Hash::check($credentials['password'], $u->password));

        if ($matched->isEmpty()) {
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
        }

        // Kalau lebih dari 1 akun cocok → tampilkan picker
        if ($matched->count() > 1) {
            $candidates = $matched->map(function ($user) {
                // Cek koneksi ke tabel ref
                $asStudent = \DB::table('ref_students')->where('user_id', $user->id)->exists();
                $asPTK     = \DB::table('ref_employes')->where('user_id', $user->id)->exists(); // sesuaikan nama tabelnya

                return [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'connected' => $asStudent ? 'Siswa' : ($asPTK ? 'PTK' : null),
                ];
            })->values();

            session([
                'account_candidates' => $matched->pluck('id')->toArray(),
                'remember_me'        => $request->boolean('remember'),
            ]);

            return back()->with('account_candidates', $candidates)->onlyInput('email');
        }

        // Hanya 1 akun → langsung login
        return $this->doLogin($matched->first(), $request->boolean('remember'));
    }

    public function selectAccount(Request $request): RedirectResponse
    {
        $request->validate(['user_id' => 'required|string']);

        $candidates = session('account_candidates', []);

        if (!in_array($request->user_id, $candidates)) {
            return redirect()->route('login')->withErrors(['email' => 'Pilihan akun tidak valid.']);
        }

        $user = User::findOrFail($request->user_id);
        $remember = session('remember_me', false);

        session()->forget(['account_candidates', 'remember_me']);

        return $this->doLogin($user, $remember);
    }

    private function doLogin(User $user, bool $remember = false): RedirectResponse
    {
        Auth::login($user, $remember);
        request()->session()->regenerate();
        $user->update(['last_login' => now()]);

        return redirect()->intended(route('drive.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}