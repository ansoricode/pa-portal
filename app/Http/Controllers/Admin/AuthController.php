<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CompletesLogin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use CompletesLogin;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email|max:150',
            'password' => 'required|string|max:200',
        ]);

        $email = Str::lower($data['email']);
        $key = 'login|' . $email . '|' . $request->ip();

        // Kunci sementara: 5 kegagalan per kombinasi email+IP -> tunggu 15 menit
        if (RateLimiter::tooManyAttempts($key, 5)) {
            ActivityLog::record('lockout', 'Login ditolak: terlalu banyak percobaan gagal', null, ['email' => Str::limit($email, 150)]);
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return back()->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$minutes} menit."])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();

        // Hash selalu dihitung agar waktu respons sama (mencegah enumerasi email)
        if ($user) {
            $valid = Hash::check($data['password'], $user->password);
        } else {
            Hash::make(Str::random(12));
            $valid = false;
        }

        if (! $valid || ! $user->is_active) {
            RateLimiter::hit($key, 900);
            ActivityLog::record('login_failed', 'Login gagal', null, ['email' => Str::limit($email, 150)]);

            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
        }

        RateLimiter::clear($key);

        if ($user->hasTwoFactor()) {
            $request->session()->regenerate();
            $request->session()->put('2fa', ['id' => $user->id, 'at' => now()->timestamp]);

            return redirect()->route('admin.2fa.challenge');
        }

        return $this->completeLogin($request, $user);
    }

    public function logout(Request $request)
    {
        ActivityLog::record('logout', 'Logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function account()
    {
        return view('admin.account');
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'             => 'required|string|max:120',
            'current_password' => 'required|current_password',
            'password'         => [$user->must_change_password ? 'required' : 'nullable', Password::defaults(), 'confirmed', 'different:current_password'],
        ]);

        $user->name = $data['name'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->must_change_password = false;
            $user->password_changed_at = now();
            $user->save();
            $user->revokeSessions($request->session()->getId());
            ActivityLog::record('password_changed', 'Mengganti kata sandi sendiri');
        } else {
            $user->save();
        }

        return redirect()->route('admin.account')->with('ok', 'Akun berhasil diperbarui.');
    }
}
