<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $term = trim((string) $request->query('q', ''));
        $like = $term === '' ? null : '%'.addcslashes($term, '%_\\').'%';

        $news = News::published()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)))
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('public.news-index', ['news' => $news, 'category' => $category, 'term' => $term]);
    }

    public function show(string $slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();

        $related = News::published()->where('id', '!=', $article->id)
            ->orderByDesc('published_at')->limit(3)->get();

        return view('public.news-show', ['article' => $article, 'related' => $related]);
    }
}
