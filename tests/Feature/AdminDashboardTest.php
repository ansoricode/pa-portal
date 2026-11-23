<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\News;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_visit_trend_and_ranks_public_content(): void
    {
        config(['portal.require_2fa' => false]);
        $this->travelTo('2026-11-23 12:00:00');
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Visit::create(['date' => '2026-11-22', 'count' => 8]);
        Visit::create(['date' => '2026-11-23', 'count' => 12]);

        News::create([
            'title' => 'Berita Teratas',
            'slug' => 'berita-teratas',
            'category' => 'Informasi',
            'content' => '<p>Berita.</p>',
            'published_at' => now(),
            'is_published' => true,
            'view_count' => 40,
        ]);
        News::create([
            'title' => 'Berita Kedua',
            'slug' => 'berita-kedua',
            'category' => 'Informasi',
            'content' => '<p>Berita.</p>',
            'published_at' => now(),
            'is_published' => true,
            'view_count' => 15,
        ]);
        News::create([
            'title' => 'Draf Tidak Ditampilkan',
            'slug' => 'draf-tidak-ditampilkan',
            'category' => 'Informasi',
            'content' => '<p>Draf.</p>',
            'published_at' => now(),
            'is_published' => false,
            'view_count' => 100,
        ]);

        Announcement::create([
            'badge' => 'Info',
            'title' => 'Pengumuman Aktif',
            'published_at' => now(),
            'url' => 'https://example.test/aktif',
            'is_active' => true,
            'view_count' => 20,
        ]);
        Announcement::create([
            'badge' => 'Info',
            'title' => 'Pengumuman Nonaktif',
            'published_at' => now(),
            'url' => 'https://example.test/nonaktif',
            'is_active' => false,
            'view_count' => 100,
        ]);
        Announcement::create([
            'badge' => 'Info',
            'title' => 'Pengumuman Tanpa Tautan',
            'published_at' => now(),
            'is_active' => true,
            'view_count' => 80,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Tren Kunjungan')
            ->assertSee('30 hari terakhir')
            ->assertSee('2026-11-22: 8 pengunjung')
            ->assertSee('2026-11-23: 12 pengunjung')
            ->assertSeeInOrder(['Berita Teratas', 'Berita Kedua'])
            ->assertDontSee('Draf Tidak Ditampilkan')
            ->assertSee('Pengumuman Aktif')
            ->assertDontSee('Pengumuman Nonaktif')
            ->assertDontSee('Pengumuman Tanpa Tautan');
    }

    public function test_dashboard_only_shows_popular_content_the_user_can_manage(): void
    {
        config(['portal.require_2fa' => false]);
        $operator = User::factory()->create([
            'role' => 'operator',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($operator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Jadwal Persidangan')
            ->assertDontSee('Berita Paling Banyak Dikunjungi')
            ->assertDontSee('Pengumuman Paling Sering Diklik');
    }
}
