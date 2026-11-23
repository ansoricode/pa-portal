<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->query('aksi');
        $q = trim((string) $request->query('q'));

        $valid = fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false ? $d : null;
        $from = $valid($request->query('dari'));
        $to = $valid($request->query('sampai'));

        $logs = ActivityLog::query()
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->when($action && isset(ActivityLog::ACTIONS[$action]), fn ($query) => $query->where('action', $action))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(fn ($w) => $w->where('description', 'like', $like)
                    ->orWhere('user_name', 'like', $like)
                    ->orWhere('subject_label', 'like', $like)
                    ->orWhere('ip_address', 'like', $like));
            })
            ->orderByDesc('id')
            ->paginate(in_array((int) $request->query('per_page'), [25, 50, 100], true) ? (int) $request->query('per_page') : 25)
            ->withQueryString();

        return view('admin.logs', ['logs' => $logs, 'action' => $action, 'q' => $q, 'from' => $from, 'to' => $to]);
    }
}
