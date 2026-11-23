<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuildingPhotoSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_building_photo_and_control_its_position_and_caption(): void
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
            'slug' => 'kontak',
            'title' => 'Alamat, Kontak & Foto Gedung',
            'is_active' => true,
        ]);
        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Foto Gedung')
            ->assertSee('name="building_photo_position"', false);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'address_street' => 'Jl. Keadilan No. 1',
                'building_photo' => UploadedFile::fake()->image('gedung.jpg'),
                'building_photo_caption' => 'Gedung kantor Pengadilan Agama',
                'building_photo_position' => 'bottom',
            ])
            ->assertRedirect();

        $photoPath = Setting::get('building_photo');
        Storage::disk('public')->assertExists($photoPath);

        $response = $this->get(route('page.show', ['section' => 'profil', 'slug' => 'kontak']))
            ->assertOk()
            ->assertSee('Gedung kantor Pengadilan Agama');
        $html = $response->getContent();
        $photoPosition = strpos($html, basename($photoPath));
        $addressPosition = strpos($html, '<strong>Jalan:</strong>');

        $this->assertIsInt($photoPosition);
        $this->assertIsInt($addressPosition);
        $this->assertLessThan($photoPosition, $addressPosition);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'address_street' => 'Jl. Keadilan No. 1',
                'building_photo_caption' => 'Gedung kantor Pengadilan Agama',
                'building_photo_position' => 'top',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'key' => 'building_photo',
            'value' => $photoPath,
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'building_photo_position',
            'value' => 'top',
        ]);

        $response = $this->get(route('page.show', ['section' => 'profil', 'slug' => 'kontak']))
            ->assertOk()
            ->assertSee('Gedung kantor Pengadilan Agama');
        $html = $response->getContent();
        $photoPosition = strpos($html, basename($photoPath));
        $addressPosition = strpos($html, '<strong>Jalan:</strong>');

        $this->assertIsInt($photoPosition);
        $this->assertIsInt($addressPosition);
        $this->assertLessThan($addressPosition, $photoPosition);
    }

    public function test_building_photo_position_rejects_unknown_options(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'building_photo_position' => 'middle',
            ])
            ->assertSessionHasErrors('building_photo_position');
    }
}
