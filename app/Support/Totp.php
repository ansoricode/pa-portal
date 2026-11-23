<?php

namespace App\Support;

use PragmaRX\Google2FA\Google2FA;

/**
 * Pembungkus tipis untuk pustaka pragmarx/google2fa (TOTP RFC 6238).
 * Seluruh logika kriptografi dikerjakan pustaka; kelas ini hanya menjaga antarmuka yang dipakai aplikasi.
 *
 * Instal: composer require pragmarx/google2fa
 */
class Totp
{
    protected static function engine(): Google2FA
    {
        static $engine = null;

        return $engine ??= new Google2FA();
    }

    /** Rahasia 32 karakter base32 (160 bit). */
    public static function generateSecret(): string
    {
        return static::engine()->generateSecretKey(32);
    }

    /** URI otpauth:// untuk QR / aplikasi autentikator. */
    public static function uri(string $secret, string $account, string $issuer): string
    {
        return static::engine()->getQRCodeUrl($issuer, $account, $secret);
    }

    /**
     * Verifikasi kode 6 digit. Mengembalikan nomor "step" yang cocok (simpan agar kode tidak dapat dipakai ulang),
     * atau null jika salah / sudah pernah dipakai.
     */
    public static function verify(string $secret, string $code, ?int $lastStep = null, int $window = 1): ?int
    {
        $code = preg_replace('/\s+/', '', $code);
        if (! preg_match('/^\d{6}$/', $code)) {
            return null;
        }

        $result = static::engine()->verifyKeyNewer($secret, $code, $lastStep ?? 0, $window);

        if ($result === true) {
            return intdiv(time(), 30);
        }

        return is_int($result) ? $result : null;
    }
}
