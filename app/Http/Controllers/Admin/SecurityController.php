<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\Totp;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $secret = $qr = null;

        // Tahap pendaftaran: rahasia sudah dibuat tetapi belum dikonfirmasi
        if ($user->two_factor_secret && ! $user->two_factor_confirmed_at) {
            $secret = $user->two_factor_secret;
            $qr = $this->qrSvg(Totp::uri($secret, $user->email, config('portal.2fa_issuer')));
        }

        return view('admin.security', [
            'user'          => $user,
            'secret'        => $secret,
            'qr'            => $qr,
            'recoveryCodes' => session('recovery_codes'),
        ]);
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        if (! $user->hasTwoFactor()) {
            $user->forceFill([
                'two_factor_secret'         => Totp::generateSecret(),
                'two_factor_confirmed_at'   => null,
                'two_factor_recovery_codes' => null,
                'two_factor_last_step'      => null,
            ])->save();
        }

        return redirect()->route('admin.security');
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $user = $request->user();

        if (! $user->two_factor_secret || $user->two_factor_confirmed_at) {
            return redirect()->route('admin.security');
        }

        $step = Totp::verify($user->two_factor_secret, $request->input('code'));
        if ($step === null) {
            return back()->withErrors(['code' => 'Kode tidak cocok. Pastikan jam perangkat Anda akurat, lalu coba lagi.']);
        }

        $codes = $user->generateRecoveryCodes();
        $user->forceFill(['two_factor_confirmed_at' => now(), 'two_factor_last_step' => $step])->save();
        ActivityLog::record('2fa_enabled', 'Mengaktifkan autentikasi dua langkah');

        return redirect()->route('admin.security')->with('recovery_codes', $codes);
    }

    public function regenerate(Request $request)
    {
        $request->validate(['current_password' => 'required|current_password']);
        $user = $request->user();
        abort_unless($user->hasTwoFactor(), 403);

        $codes = $user->generateRecoveryCodes();
        $user->save();
        ActivityLog::record('2fa_enabled', 'Membuat ulang kode pemulihan 2FA');

        return redirect()->route('admin.security')->with('recovery_codes', $codes);
    }

    public function disable(Request $request)
    {
        if (config('portal.require_2fa')) {
            return back()->withErrors(['current_password' => '2FA diwajibkan oleh kebijakan sistem dan tidak dapat dinonaktifkan.']);
        }

        $request->validate(['current_password' => 'required|current_password']);

        $request->user()->forceFill([
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
            'two_factor_last_step'      => null,
        ])->save();
        ActivityLog::record('2fa_disabled', 'Menonaktifkan autentikasi dua langkah');

        return redirect()->route('admin.security')->with('ok', '2FA dinonaktifkan.');
    }

    /** QR berbentuk SVG (butuh paket bacon/bacon-qr-code). Jika tidak ada, halaman tetap menampilkan kunci manual. */
    protected function qrSvg(string $uri): ?string
    {
        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200, 1),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $svg = (new \BaconQrCode\Writer($renderer))->writeString($uri);

            return preg_replace('/^<\?xml.*?\?>\s*/s', '', $svg);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
