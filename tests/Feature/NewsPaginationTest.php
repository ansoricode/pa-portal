<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_shows_ten_articles_per_page_with_theme_pagination(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            News::create([
                'title' => "Berita Contoh $i",
                'slug' => "berita-contoh-$i",
                'category' => 'Kegiatan',
                'excerpt' => "Ringkasan berita contoh $i.",
                'content' => "<p>Isi berita contoh $i.</p>",
                'published_at' => now()->subDays($i),
                'is_published' => true,
            ]);
        }

        $firstPage = $this->get(route('news.index'))
            ->assertOk()
            ->assertSee('class="news-pagination mt-4"', false)
            ->assertSee('page-item active', false)
            ->assertSee('page-link', false);
        $firstPageNews = $firstPage->viewData('news');

        $this->assertSame(10, $firstPageNews->perPage());
        $this->assertSame(12, $firstPageNews->total());
        $this->assertCount(10, $firstPageNews->items());

        $secondPage = $this->get(route('news.index', ['page' => 2]))
            ->assertOk()
            ->viewData('news');

        $this->assertCount(2, $secondPage->items());
        $this->assertSame(11, $secondPage->firstItem());
    }
}
