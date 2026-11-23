<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait CompletesLogin
{
    protected function completeLogin(Request $request, User $user)
    {
        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();
        ActivityLog::record('login', 'Login berhasil', null, [], $user);

        return redirect()->intended(route('admin.dashboard'));
    }
}
