<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $like = $q === '' ? null : '%' . addcslashes($q, '%_\\') . '%';

        $announcements = Announcement::where('is_active', true)
            ->when($like, fn ($x) => $x->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('badge', 'like', $like)))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('public.announcements', compact('announcements'));
    }
}
