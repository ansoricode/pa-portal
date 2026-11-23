<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentVisitCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_detail_counts_visitors_once_per_session_per_day(): void
    {
        $this->travelTo('2026-11-23 12:00:00');
        $article = News::create([
            'title' => 'Berita Pengujian',
            'slug' => 'berita-pengujian',
            'content' => '<p>Isi berita.</p>',
            'published_at' => now(),
            'is_published' => true,
        ]);

        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee('0 pengunjung');

        $this->get(route('news.show', $article->slug))
            ->assertOk()
            ->assertSee('1 pengunjung');

        $this->get(route('news.show', $article->slug))
            ->assertOk()
            ->assertSee('1 pengunjung');

        $this->assertDatabaseHas('news', [
            'id' => $article->id,
            'view_count' => 1,
        ]);
    }

    public function test_announcement_click_counts_visitors_once_and_redirects_to_its_url(): void
    {
        $this->travelTo('2026-11-23 12:00:00');
        $announcement = Announcement::create([
            'badge' => 'Pengumuman',
            'title' => 'Pengumuman Pengujian',
            'published_at' => now(),
            'url' => 'https://example.test/pengumuman',
            'is_active' => true,
        ]);

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('0 pengunjung');

        $this->get(route('announcements.visit', $announcement))
            ->assertRedirect($announcement->link);

        $this->get(route('announcements.visit', $announcement))
            ->assertRedirect($announcement->link);

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('1 pengunjung');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'view_count' => 1,
        ]);
    }

    public function test_inactive_announcement_cannot_be_visited(): void
    {
        $announcement = Announcement::create([
            'badge' => 'Pengumuman',
            'title' => 'Pengumuman Nonaktif',
            'url' => 'https://example.test/pengumuman',
            'is_active' => false,
        ]);

        $this->get(route('announcements.visit', $announcement))
            ->assertNotFound();

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'view_count' => 0,
        ]);
    }

    public function test_admin_lists_show_per_item_visitor_totals(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        News::create([
            'title' => 'Berita Admin',
            'slug' => 'berita-admin',
            'content' => '<p>Isi berita.</p>',
            'published_at' => now(),
            'is_published' => true,
            'view_count' => 27,
        ]);
        Announcement::create([
            'badge' => 'Pengumuman',
            'title' => 'Pengumuman Admin',
            'published_at' => now(),
            'url' => 'https://example.test/pengumuman',
            'is_active' => true,
            'view_count' => 42,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resource.index', 'news'))
            ->assertOk()
            ->assertSee('Pengunjung')
            ->assertSee('27');

        $this->get(route('admin.resource.index', 'announcements'))
            ->assertOk()
            ->assertSee('Pengunjung')
            ->assertSee('42');
    }
}
