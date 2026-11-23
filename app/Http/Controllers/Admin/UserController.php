<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when(trim((string) $request->query('q')) !== '', function ($q) use ($request) {
                $like = '%' . addcslashes(trim($request->query('q')), '%_\\') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'email'     => 'required|email|max:150|unique:users,email',
            'role'      => ['required', Rule::in(array_keys(config('portal.roles')))],
            'password'  => ['required', Password::defaults()],
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => strtolower($data['email']),
            'role'                 => $data['role'],
            'password'             => Hash::make($data['password']),
            'is_active'            => $request->boolean('is_active'),
            'must_change_password' => true,
        ]);

        ActivityLog::record('user_created', "Membuat pengguna {$user->email} ({$user->role})", $user);

        return redirect()->route('admin.users.index')->with('ok', 'Pengguna dibuat. Yang bersangkutan wajib mengganti sandi dan mengaktifkan 2FA saat login pertama.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'email'     => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role'      => ['required', Rule::in(array_keys(config('portal.roles')))],
            'password'  => ['nullable', Password::defaults()],
            'is_active' => 'nullable|boolean',
        ]);

        $active = $request->boolean('is_active');
        $isSelf = $user->id === $request->user()->id;

        if ($isSelf && (! $active || $data['role'] !== $user->role)) {
            return back()->withInput()->withErrors(['role' => 'Anda tidak dapat menonaktifkan akun sendiri atau mengubah peran sendiri.']);
        }
        if ($this->wouldRemoveLastSuperAdmin($user, $data['role'], $active)) {
            return back()->withInput()->withErrors(['role' => 'Harus tersisa minimal satu Super Admin aktif.']);
        }

        $changes = [];
        foreach (['name', 'email', 'role'] as $f) {
            if ($user->{$f} !== $data[$f]) {
                $changes[$f] = ['dari' => $user->{$f}, 'ke' => $data[$f]];
            }
        }
        if ($user->is_active !== $active) {
            $changes['is_active'] = ['dari' => $user->is_active, 'ke' => $active];
        }

        $user->fill(['name' => $data['name'], 'email' => strtolower($data['email']), 'role' => $data['role'], 'is_active' => $active]);

        $mustRevoke = ! $active || isset($changes['role']);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->must_change_password = true;
            $user->password_changed_at = now();
            $changes['password'] = 'direset oleh admin';
            $mustRevoke = true;
        }
        $user->save();

        if ($mustRevoke) {
            $user->revokeSessions($isSelf ? $request->session()->getId() : null);
        }

        ActivityLog::record('user_updated', "Mengubah pengguna {$user->email}", $user, $changes);

        return redirect()->route('admin.users.index')->with('ok', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['db' => 'Anda tidak dapat menghapus akun sendiri.']);
        }
        if ($this->wouldRemoveLastSuperAdmin($user, null, false)) {
            return back()->withErrors(['db' => 'Harus tersisa minimal satu Super Admin aktif.']);
        }

        $user->revokeSessions();
        ActivityLog::record('user_deleted', "Menghapus pengguna {$user->email}", $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('ok', 'Pengguna dihapus.');
    }

    /** Reset 2FA (mis. perangkat hilang dan kode pemulihan habis). Pengguna wajib mendaftar ulang saat login berikutnya. */
    public function reset2fa(Request $request, User $user)
    {
        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_confirmed_at'   => null,
            'two_factor_recovery_codes' => null,
            'two_factor_last_step'      => null,
        ])->save();
        $user->revokeSessions($user->id === $request->user()->id ? $request->session()->getId() : null);

        ActivityLog::record('2fa_reset', "Mereset 2FA pengguna {$user->email}", $user);

        return back()->with('ok', '2FA pengguna direset. Yang bersangkutan harus mendaftarkan ulang.');
    }

    protected function wouldRemoveLastSuperAdmin(User $user, ?string $newRole, bool $newActive): bool
    {
        if (! $user->isSuperAdmin() || ! $user->is_active) {
            return false;
        }
        if ($newRole === 'superadmin' && $newActive) {
            return false;
        }

        return User::where('role', 'superadmin')->where('is_active', true)->where('id', '!=', $user->id)->count() === 0;
    }
}
