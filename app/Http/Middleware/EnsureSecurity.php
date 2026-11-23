<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Untuk semua halaman panel: akun harus aktif, sandi default harus diganti, dan 2FA wajib bila diaktifkan kebijakan. */
class EnsureSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan.']);
        }

        if ($user && ! $request->routeIs('admin.logout')) {
            // Tahap 1: sandi sementara wajib diganti
        if ($user->must_change_password) {
            if (! $request->routeIs('admin.account', 'admin.account.update')) {
                return redirect()->route('admin.account')->with('warning', 'Anda wajib mengganti kata sandi sebelum melanjutkan.');
            }

            return $next($request);
            }

        // Tahap 2: 2FA wajib diaktifkan
        if (config('portal.require_2fa') && ! $user->hasTwoFactor() && ! $request->routeIs('admin.security*')) {
            return redirect()->route('admin.security')->with('warning', 'Aktifkan autentikasi dua langkah (2FA) untuk melanjutkan.');
            }
        }

        return $next($request);
    }
}
