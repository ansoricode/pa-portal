<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $like = $q === '' ? null : '%'.addcslashes($q, '%_\\').'%';

        $announcements = Announcement::where('is_active', true)
            ->when($like, fn ($x) => $x->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('badge', 'like', $like)))
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('public.announcements', compact('announcements'));
    }

    public function visit(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($announcement->is_active, 404);

        if (! $announcement->url) {
            return redirect()->route('announcements.index');
        }

        $visitKey = 'announcement_visited_'.$announcement->id.'_'.now()->toDateString();

        if (! $request->session()->has($visitKey)) {
            $announcement->increment('view_count');
            $request->session()->put($visitKey, true);
        }

        return redirect()->to($announcement->link);
    }
}
