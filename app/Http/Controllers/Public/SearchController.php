<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $empty = fn () => new LengthAwarePaginator([], 0, 10);
        $pages = $news = $documents = $empty();

        if (mb_strlen($q) >= 2) {
            $like = '%' . addcslashes($q, '%_\\') . '%';

            $pages = Page::where('is_active', true)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('content', 'like', $like))
                ->orderBy('title')
                ->paginate(10, ['*'], 'halaman_page')->withQueryString();

            $news = News::published()
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('content', 'like', $like))
                ->orderByDesc('published_at')
                ->paginate(10, ['*'], 'berita_page')->withQueryString();

            $documents = Document::where('is_active', true)
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like))
                ->orderBy('category')->orderBy('sort_order')
                ->paginate(10, ['*'], 'dokumen_page')->withQueryString();
        }

        return view('public.search', compact('q', 'pages', 'news', 'documents'));
    }
}
