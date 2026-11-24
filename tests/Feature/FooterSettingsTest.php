<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FooterSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_manage_footer_and_public_page_renders_links(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($superadmin)
            ->get(route('admin.footer.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Footer')
            ->assertSee('Judul Tautan Footer')
            ->assertSee('Judul Ekosistem Peradilan')
            ->assertSee('Judul Statistik Kunjungan')
            ->assertSee('Media Sosial Footer')
            ->assertSee('data-add-row="social-links"', false)
            ->assertSee('data-add-row="footer-links"', false)
            ->assertSee('data-add-row="ecosystem-links"', false)
            ->assertSee("document.querySelectorAll('[data-add-row]')", false);

        $this->actingAs($superadmin)
            ->put(route('admin.footer.update'), [
                'footer_hours' => 'Senin-Jumat, 08.00-16.00 WIB',
                'footer_copyright' => '&copy; 2026 Pengadilan Agama Contoh',
                'footer_links_title' => 'Tautan Pilihan',
                'footer_ecosystem_title' => 'Ekosistem Peradilan',
                'footer_stats_title' => 'Jumlah Pengunjung',
                'facebook_url' => 'https://facebook.com/pacontoh',
                'instagram_url' => 'https://instagram.com/pacontoh',
                'youtube_url' => 'https://youtube.com/@pacontoh',
                'social_links' => [
                    [
                        'name' => 'TikTok',
                        'url' => 'https://tiktok.com/@pacontoh',
                        'icon' => 'bi-tiktok',
                        'sort_order' => 10,
                        'is_active' => '1',
                    ],
                    [
                        'name' => 'Facebook',
                        'url' => 'https://facebook.com/pacontoh',
                        'icon' => 'FB',
                        'sort_order' => 20,
                        'is_active' => '1',
                    ],
                ],
                'footer_links' => [
                    [
                        'name' => 'Layanan Informasi',
                        'url' => 'https://contoh.go.id/layanan',
                        'description' => 'Akses layanan publik',
                        'icon' => 'bi-info-circle',
                        'sort_order' => 5,
                        'is_active' => '1',
                        'icon_file' => UploadedFile::fake()->image('icon.png', 40, 40),
                    ],
                ],
                'ecosystem_links' => [
                    [
                        'name' => 'Ekosistem Uji',
                        'url' => 'https://ecosystem.go.id',
                        'icon' => 'bi-bank',
                        'sort_order' => 1,
                        'is_active' => '1',
                    ],
                ],
            ])
            ->assertRedirect();

        $links = json_decode((string) Setting::get('footer_links'), true);
        $ecosystem = json_decode((string) Setting::get('footer_ecosystem'), true);
        $social = json_decode((string) Setting::get('footer_social'), true);

        $this->assertSame('Senin-Jumat, 08.00-16.00 WIB', Setting::get('footer_hours'));
        $this->assertSame('Tautan Pilihan', Setting::get('footer_links_title'));
        $this->assertSame('Ekosistem Peradilan', Setting::get('footer_ecosystem_title'));
        $this->assertSame('Jumlah Pengunjung', Setting::get('footer_stats_title'));
        $this->assertSame('TikTok', $social[0]['name']);
        $this->assertSame('https://facebook.com/pacontoh', Setting::get('facebook_url'));
        $this->assertSame('Layanan Informasi', $links[0]['name']);
        Storage::disk('public')->assertExists($links[0]['icon']);
        $this->assertSame('Ekosistem Uji', $ecosystem[0]['name']);
        $this->assertSame('bi-bank', $ecosystem[0]['icon']);
        $this->assertSame('', $ecosystem[0]['logo']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Layanan Informasi')
            ->assertSee('Ekosistem Uji')
            ->assertSee('Tautan Pilihan')
            ->assertSee('Ekosistem Peradilan')
            ->assertSee('Jumlah Pengunjung')
            ->assertSee('TikTok')
            ->assertSee('bi-bank')
            ->assertSee('bi-tiktok')
            ->assertSee('bi-facebook')
            ->assertDontSee('<span class="small">FB</span>', false)
            ->assertSee('Senin-Jumat, 08.00-16.00 WIB');
    }

    public function test_regular_admin_cannot_open_footer_settings(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.footer.edit'))
            ->assertForbidden();
    }
}
