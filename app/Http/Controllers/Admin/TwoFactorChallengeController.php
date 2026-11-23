<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\CompletesLogin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorChallengeController extends Controller
{
    use CompletesLogin;

    public function show(Request $request)
    {
        if (! $this->pendingUser($request)) {
            return redirect()->route('login');
        }

        return view('admin.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user || ! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi berakhir. Silakan login kembali.']);
        }

        $request->validate(['code' => 'required|string|max:32']);

        $key = '2fa|' . $user->id . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $request->session()->forget('2fa');
            ActivityLog::record('lockout', 'Verifikasi 2FA ditolak: terlalu banyak percobaan', null, [], $user);

            return redirect()->route('login')->withErrors(['email' => 'Terlalu banyak percobaan verifikasi. Coba lagi beberapa menit lagi.']);
        }

        $code = trim((string) $request->input('code'));
        $step = Totp::verify($user->two_factor_secret, $code, $user->two_factor_last_step);

        if ($step !== null) {
            $user->forceFill(['two_factor_last_step' => $step])->save();
        } elseif ($user->consumeRecoveryCode($code)) {
            ActivityLog::record('recovery_used', 'Login memakai kode pemulihan 2FA', null, ['sisa_kode' => count($user->two_factor_recovery_codes ?? [])], $user);
        } else {
            RateLimiter::hit($key, 300);
            ActivityLog::record('2fa_failed', 'Kode 2FA salah', null, [], $user);

            return back()->withErrors(['code' => 'Kode verifikasi salah.']);
        }

        RateLimiter::clear($key);
        $request->session()->forget('2fa');

        return $this->completeLogin($request, $user);
    }

    /** Pengguna yang sudah lolos sandi tetapi belum lolos 2FA (berlaku 5 menit). */
    protected function pendingUser(Request $request): ?User
    {
        $state = $request->session()->get('2fa');

        if (! $state || now()->timestamp - ($state['at'] ?? 0) > 300) {
            $request->session()->forget('2fa');

            return null;
        }

        return User::find($state['id']);
    }
}
