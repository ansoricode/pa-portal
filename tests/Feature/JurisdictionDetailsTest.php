<?php

namespace Tests\Feature;

use App\Models\Jurisdiction;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurisdictionDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_villages_radius_and_drive_link_and_frontend_embeds_it(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create(['role' => 'superadmin', 'is_active' => true, 'must_change_password' => false]);
        Page::create(['section' => 'profil', 'slug' => 'yurisdiksi', 'title' => 'Yurisdiksi', 'content' => '<p>Isi</p>', 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.resource.create', 'jurisdictions'))
            ->assertOk()
            ->assertSee('data-tag-input', false)
            ->assertSee('data-map-check', false);

        $this->actingAs($admin)
            ->post(route('admin.resource.store', 'jurisdictions'), [
                'name' => 'Kecamatan Contoh',
                'villages' => "Desa A\nDesa B",
                'radius' => '15 km',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.resource.index', 'jurisdictions'));

        $this->get(route('page.show', ['section' => 'profil', 'slug' => 'yurisdiksi']))
            ->assertOk()
            ->assertSee('Desa A')
            ->assertSee('Desa B')
            ->assertSee('2 desa/kelurahan')
            ->assertSee('15 km')
            ->assertSee('https://www.google.com/maps/embed?pb=!1m18!1m12', false);
    }

    public function test_map_link_must_be_a_google_maps_url(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create(['role' => 'superadmin', 'is_active' => true, 'must_change_password' => false]);

        $this->actingAs($admin)
            ->post(route('admin.resource.store', 'jurisdictions'), [
                'name' => 'Kecamatan Contoh',
                'map_url' => 'https://evil.example.com/maps/embed',
            ])
            ->assertSessionHasErrors('map_url');
    }

    public function test_embed_url_supports_embed_place_coordinate_and_unknown_links(): void
    {
        $embed = fn (string $url) => (new Jurisdiction(['map_url' => $url]))->map_embed_url;

        $this->assertSame('https://www.google.com/maps/embed?pb=ABC', $embed('https://www.google.com/maps/embed?pb=ABC'));
        $this->assertSame('https://www.google.com/maps?q=Pengadilan+Agama+Madiun&output=embed', $embed('https://www.google.com/maps/place/Pengadilan+Agama+Madiun/@-7.6,111.5,17z'));
        $this->assertSame('https://www.google.com/maps?q=-7.6298,111.5239&output=embed', $embed('https://www.google.com/maps/@-7.6298,111.5239,15z'));
        $this->assertNull($embed('https://www.google.com/maps'));
    }
}
