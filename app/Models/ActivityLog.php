<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['properties' => 'array', 'created_at' => 'datetime'];

    public const ACTIONS = [
        'login'            => ['Login berhasil', 'success'],
        'login_failed'     => ['Login gagal', 'danger'],
        'lockout'          => ['Akun terkunci sementara', 'danger'],
        'logout'           => ['Logout', 'secondary'],
        '2fa_failed'       => ['Kode 2FA salah', 'danger'],
        '2fa_enabled'      => ['2FA diaktifkan', 'info'],
        '2fa_disabled'     => ['2FA dinonaktifkan', 'warning'],
        '2fa_reset'        => ['2FA direset admin', 'warning'],
        'recovery_used'    => ['Kode pemulihan dipakai', 'warning'],
        'password_changed' => ['Kata sandi diubah', 'info'],
        'create'           => ['Tambah data', 'primary'],
        'update'           => ['Ubah data', 'primary'],
        'delete'           => ['Hapus data', 'dark'],
        'settings_updated' => ['Ubah pengaturan', 'primary'],
        'user_created'     => ['Pengguna dibuat', 'info'],
        'user_updated'     => ['Pengguna diubah', 'info'],
        'user_deleted'     => ['Pengguna dihapus', 'dark'],
    ];

    /** Catat aktivitas. Tidak pernah melempar galat agar tidak mengganggu proses utama. */
    public static function record(string $action, string $description, ?Model $subject = null, array $properties = [], ?User $user = null): void
    {
        try {
            $user = $user ?: auth()->user();
            $request = request();

            static::create([
                'user_id'       => $user?->id,
                'user_name'     => $user?->name,
                'action'        => $action,
                'description'   => Str::limit($description, 480),
                'subject_type'  => $subject ? class_basename($subject) : null,
                'subject_id'    => $subject?->getKey(),
                'subject_label' => $subject ? static::label($subject) : null,
                'properties'    => $properties ?: null,
                'ip_address'    => $request?->ip(),
                'user_agent'    => Str::limit((string) $request?->userAgent(), 250, ''),
                'created_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected static function label(Model $model): string
    {
        foreach (['title', 'name', 'party_name', 'case_number', 'key'] as $attr) {
            if (! empty($model->{$attr})) {
                return Str::limit((string) $model->{$attr}, 120);
            }
        }

        return '#' . $model->getKey();
    }
}
