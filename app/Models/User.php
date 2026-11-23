<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'must_change_password'];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    protected $casts = [
        'email_verified_at'         => 'datetime',
        'is_active'                 => 'boolean',
        'must_change_password'      => 'boolean',
        'two_factor_secret'         => 'encrypted',
        'two_factor_recovery_codes' => 'array',
        'two_factor_confirmed_at'   => 'datetime',
        'last_login_at'             => 'datetime',
        'password_changed_at'       => 'datetime',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function roleLabel(): string
    {
        return config("portal.roles.{$this->role}.label", $this->role);
    }

    /** Boleh mengelola menu CRUD tertentu (kunci di config/admin_resources.php). */
    public function canManage(string $resource): bool
    {
        $allowed = config("portal.roles.{$this->role}.resources", []);

        return $allowed === '*' || in_array($resource, (array) $allowed, true);
    }

    /** Izin khusus: users | logs | settings */
    public function hasPermission(string $permission): bool
    {
        return (bool) config("portal.roles.{$this->role}.$permission", false);
    }

    public function hasTwoFactor(): bool
    {
        return ! empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    /** Buat 8 kode pemulihan baru. Mengembalikan kode asli (tampil sekali); yang disimpan hanya hash-nya. Panggil save() setelahnya. */
    public function generateRecoveryCodes(): array
    {
        $plain = [];
        for ($i = 0; $i < 8; $i++) {
            $plain[] = Str::lower(Str::random(5)) . '-' . Str::lower(Str::random(5));
        }
        $this->two_factor_recovery_codes = array_map(fn ($c) => Hash::make($c), $plain);

        return $plain;
    }

    /** Pakai satu kode pemulihan (sekali pakai). */
    public function consumeRecoveryCode(string $code): bool
    {
        $code = Str::lower(trim($code));
        $hashes = $this->two_factor_recovery_codes ?? [];

        foreach ($hashes as $i => $hash) {
            if (Hash::check($code, $hash)) {
                unset($hashes[$i]);
                $this->two_factor_recovery_codes = array_values($hashes);
                $this->save();

                return true;
            }
        }

        return false;
    }

    /** Putuskan sesi lain milik pengguna ini (hanya efektif jika SESSION_DRIVER=database). */
    public function revokeSessions(?string $exceptSessionId = null): void
    {
        $this->setRememberToken(Str::random(60));
        $this->save();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $this->id)
                ->when($exceptSessionId, fn ($q) => $q->where('id', '!=', $exceptSessionId))
                ->delete();
        }
    }
}
