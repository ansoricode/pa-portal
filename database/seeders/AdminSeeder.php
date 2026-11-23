<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Atur lewat .env: ADMIN_EMAIL dan ADMIN_PASSWORD. Jika sandi kosong, dibuat acak dan DITAMPILKAN SEKALI di terminal.
        $email = strtolower(env('ADMIN_EMAIL', 'admin@pa-contoh.go.id'));
        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (! $password) {
            $password = 'Pa-' . Str::random(14) . '#7a';
            $generated = true;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'                 => 'Super Administrator',
                'password'             => Hash::make($password),
                'role'                 => 'superadmin',
                'is_active'            => true,
                'must_change_password' => true,
            ]
        );

        $this->command?->info("Super Admin: {$email}");
        if ($generated) {
            $this->command?->warn("Kata sandi sementara (catat, hanya tampil sekali): {$password}");
        }
    }
}
