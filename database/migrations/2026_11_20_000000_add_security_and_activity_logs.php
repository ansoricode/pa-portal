<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role', 30)->default('admin');
            $t->boolean('is_active')->default(true);
            $t->boolean('must_change_password')->default(false);
            $t->text('two_factor_secret')->nullable();
            $t->text('two_factor_recovery_codes')->nullable();
            $t->timestamp('two_factor_confirmed_at')->nullable();
            $t->unsignedBigInteger('two_factor_last_step')->nullable();
            $t->timestamp('last_login_at')->nullable();
            $t->string('last_login_ip', 45)->nullable();
            $t->timestamp('password_changed_at')->nullable();
        });

        // Instalasi lama: pengguna pertama dijadikan Super Admin agar tidak terkunci.
        $first = DB::table('users')->orderBy('id')->value('id');
        if ($first) {
            DB::table('users')->where('id', $first)->update(['role' => 'superadmin']);
        }

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->string('user_name')->nullable();
            $t->string('action', 40)->index();
            $t->string('description', 500);
            $t->string('subject_type', 60)->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->string('subject_label')->nullable();
            $t->json('properties')->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->string('user_agent', 255)->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['role', 'is_active', 'must_change_password', 'two_factor_secret', 'two_factor_recovery_codes',
                'two_factor_confirmed_at', 'two_factor_last_step', 'last_login_at', 'last_login_ip', 'password_changed_at']);
        });
    }
};
