@extends('admin.layout')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 fw-bold mb-0"><i class="bi bi-person-gear me-2" style="color:#056e4f"></i>Manajemen Pengguna</h1>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-pa"><i class="bi bi-plus-lg me-1"></i>Tambah Pengguna</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama</th><th>Email</th><th>Peran</th><th>2FA</th><th>Status</th><th>Login Terakhir</th><th class="text-end" style="width:130px">Aksi</th></tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }} @if($u->id === auth()->id())<span class="badge bg-light text-dark border">Anda</span>@endif</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-{{ $u->isSuperAdmin() ? 'danger' : 'secondary' }}">{{ $u->roleLabel() }}</span></td>
                    <td><span class="badge {{ $u->hasTwoFactor() ? 'bg-success' : 'bg-warning text-dark' }}">{{ $u->hasTwoFactor() ? 'Aktif' : 'Belum' }}</span></td>
                    <td><span class="badge {{ $u->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="small text-secondary">{{ $u->last_login_at?->translatedFormat('d M Y H:i') ?? '-' }}<br>{{ $u->last_login_ip }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-success" title="Ubah"><i class="bi bi-pencil"></i></a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" data-confirm="Hapus pengguna {{ $u->email }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
