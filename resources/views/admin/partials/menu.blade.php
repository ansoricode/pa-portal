@php
    $u = auth()->user();
    $groups = collect(config('admin_resources'))->filter(fn ($d, $k) => $u->canManage($k))->groupBy('group', true);
    $current = request()->route('resource');
@endphp
<a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
<a href="{{ route('admin.compliance.index') }}" class="{{ request()->routeIs('admin.compliance.*') ? 'active' : '' }}"><i class="bi bi-clipboard2-check me-2"></i>Monitoring Kepatuhan</a>
@foreach($groups as $group => $defs)
    <div class="grp">{{ $group }}</div>
    @foreach($defs as $key => $d)
        <a href="{{ route('admin.resource.index', $key) }}" class="{{ $current === $key ? 'active' : '' }}"><i class="bi {{ $d['icon'] }} me-2"></i>{{ $d['label'] }}</a>
    @endforeach
@endforeach
<div class="grp">Sistem</div>
@if($u->hasPermission('settings'))
    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i class="bi bi-gear me-2"></i>Pengaturan Situs</a>
    @if($u->isSuperAdmin())
        <a href="{{ route('admin.footer.edit') }}" class="{{ request()->routeIs('admin.footer.*') ? 'active' : '' }}"><i class="bi bi-layout-text-window-reverse me-2"></i>Pengaturan Footer</a>
    @endif
@endif
@if($u->hasPermission('users'))
    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-person-gear me-2"></i>Manajemen Pengguna</a>
@endif
@if($u->hasPermission('logs'))
    <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}"><i class="bi bi-journal-text me-2"></i>Log Aktivitas</a>
@endif
<a href="{{ route('admin.security') }}" class="{{ request()->routeIs('admin.security*') ? 'active' : '' }}"><i class="bi bi-shield-lock me-2"></i>Keamanan (2FA)</a>
<a href="{{ route('admin.account') }}" class="{{ request()->routeIs('admin.account*') ? 'active' : '' }}"><i class="bi bi-person-circle me-2"></i>Akun Saya</a>
