<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Shortcut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_seo_settings_and_public_pages_render_metadata(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        Page::create([
            'section' => 'profil',
            'slug' => 'tupoksi',
            'title' => 'Tugas Pokok dan Fungsi',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('SEO & Tampilan')
            ->assertSee('Favicon Situs (PNG/ICO)')
            ->assertDontSee('Ikon Facebook ditampilkan');

        $this->put(route('admin.settings.update'), [
            'site_name' => 'Pengadilan Agama Contoh',
            'seo_title' => 'Portal Resmi Pengadilan Agama Contoh',
            'site_description' => 'Informasi resmi dan layanan Pengadilan Agama Contoh.',
            'seo_keywords' => 'pengadilan agama, layanan publik',
            'favicon' => UploadedFile::fake()->image('favicon.png', 32, 32),
            'og_image' => UploadedFile::fake()->image('pratinjau.png', 1200, 630),
        ])->assertRedirect();

        $faviconPath = Setting::get('favicon');
        $ogImagePath = Setting::get('og_image');
        Storage::disk('public')->assertExists($faviconPath);
        Storage::disk('public')->assertExists($ogImagePath);

        $this->get(route('page.show', ['section' => 'profil', 'slug' => 'tupoksi']))
            ->assertOk()
            ->assertSee('<title>Tugas Pokok dan Fungsi | Portal Resmi Pengadilan Agama Contoh</title>', false)
            ->assertSee('<meta name="description" content="Informasi resmi dan layanan Pengadilan Agama Contoh.">', false)
            ->assertSee('<meta name="keywords" content="pengadilan agama, layanan publik">', false)
            ->assertSee('<link rel="icon" href="'.asset('storage/'.$faviconPath).'" type="image/png">', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/'.$ogImagePath).'">', false);

        News::create([
            'title' => 'Berita Uji SEO',
            'slug' => 'berita-uji-seo',
            'excerpt' => 'Ringkasan berita yang digunakan pada metadata.',
            'content' => '<p>Isi berita untuk pengujian metadata.</p>',
            'image' => $ogImagePath,
            'published_at' => now(),
            'is_published' => true,
        ]);
        $this->get(route('news.show', 'berita-uji-seo'))
            ->assertOk()
            ->assertSee('<title>Berita Uji SEO | Portal Resmi Pengadilan Agama Contoh</title>', false)
            ->assertSee('<meta name="description" content="Ringkasan berita yang digunakan pada metadata.">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/'.$ogImagePath).'">', false);
    }

    public function test_favicon_upload_rejects_non_image_files(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'favicon' => UploadedFile::fake()->create('favicon.txt', 1, 'text/plain'),
        ])->assertSessionHasErrors('favicon');
    }

    public function test_favicon_is_used_on_admin_and_authentication_pages_with_a_working_default(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Setting::put('favicon', null);
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<link rel="icon" href="'.asset('favicon.svg').'" type="image/svg+xml">', false);

        Setting::put('favicon', 'uploads/settings/custom.png');
        $expected = '<link rel="icon" href="'.asset('storage/uploads/settings/custom.png').'" type="image/png">';

        $this->get(route('login'))->assertOk()->assertSee($expected, false);
        $this->withSession(['2fa' => ['id' => $admin->id, 'at' => now()->timestamp]])
            ->get(route('admin.2fa.challenge'))->assertOk()->assertSee($expected, false);
        $this->actingAs($admin)->get(route('admin.settings'))->assertOk()->assertSee($expected, false);
    }

    public function test_header_social_and_whatsapp_settings_are_rendered(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Setting::put('whatsapp', '0812-3456-7890');
        Setting::put('facebook_url', 'https://facebook.com/pacontoh');
        Setting::put('instagram_url', 'https://instagram.com/pacontoh');
        Setting::put('youtube_url', 'https://youtube.com/@pacontoh');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('WhatsApp PTSP')
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('https://facebook.com/pacontoh', false)
            ->assertSee('https://instagram.com/pacontoh', false)
            ->assertSee('https://youtube.com/@pacontoh', false);
    }

    public function test_sipp_setting_updates_the_homepage_shortcut_url(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        Shortcut::create([
            'title' => 'SIPP Satker',
            'description' => 'Sistem Informasi Penelusuran Perkara',
            'icon' => 'bi-search',
            'url' => 'https://sipp-lama.example.test',
            'is_external' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $newSippUrl = 'https://sipp-baru.example.test';

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'sipp_url' => $newSippUrl,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'key' => 'sipp_url',
            'value' => $newSippUrl,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.$newSippUrl.'"', false)
            ->assertDontSee('href="https://sipp-lama.example.test"', false);
    }
}
