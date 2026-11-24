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
            ->assertSee('Pengaturan Footer');

        $this->actingAs($superadmin)
            ->put(route('admin.footer.update'), [
                'footer_hours' => 'Senin-Jumat, 08.00-16.00 WIB',
                'footer_copyright' => '&copy; 2026 Pengadilan Agama Contoh',
                'facebook_url' => 'https://facebook.com/pacontoh',
                'instagram_url' => 'https://instagram.com/pacontoh',
                'youtube_url' => 'https://youtube.com/@pacontoh',
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
                        'sort_order' => 1,
                        'is_active' => '1',
                        'logo_file' => UploadedFile::fake()->image('logo.png', 40, 40),
                    ],
                ],
            ])
            ->assertRedirect();

        $links = json_decode((string) Setting::get('footer_links'), true);
        $ecosystem = json_decode((string) Setting::get('footer_ecosystem'), true);

        $this->assertSame('Senin-Jumat, 08.00-16.00 WIB', Setting::get('footer_hours'));
        $this->assertSame('Layanan Informasi', $links[0]['name']);
        Storage::disk('public')->assertExists($links[0]['icon']);
        $this->assertSame('Ekosistem Uji', $ecosystem[0]['name']);
        Storage::disk('public')->assertExists($ecosystem[0]['logo']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Layanan Informasi')
            ->assertSee('Ekosistem Uji')
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
