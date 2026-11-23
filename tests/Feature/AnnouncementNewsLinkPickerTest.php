<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementNewsLinkPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_form_can_select_live_news_and_keeps_manual_url_field(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        News::create([
            'title' => 'Berita yang tayang',
            'slug' => 'berita-yang-tayang',
            'content' => '<p>Isi berita.</p>',
            'published_at' => now(),
            'is_published' => true,
        ]);
        News::create([
            'title' => 'Berita draf',
            'slug' => 'berita-draf',
            'content' => '<p>Isi draf.</p>',
            'published_at' => now(),
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resource.create', 'announcements'))
            ->assertOk()
            ->assertSee('Pilih berita yang sudah tayang (opsional)')
            ->assertSee('data-news-url-picker="f_url"', false)
            ->assertSee('value="/berita/berita-yang-tayang"', false)
            ->assertDontSee('value="/berita/berita-draf"', false)
            ->assertSee('name="url"', false)
            ->assertSee('URL manual tetap bisa digunakan.');
    }
}
