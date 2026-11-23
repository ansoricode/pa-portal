<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_remove_page_media_and_frontend_displays_it(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $page = Page::create([
            'section' => 'profil',
            'slug' => 'visi-misi',
            'title' => 'Visi dan Misi',
            'content' => '<p>Isi halaman</p>',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resource.edit', ['resource' => 'pages', 'id' => $page->id]))
            ->assertOk()
            ->assertSee('Galeri Media')
            ->assertSee('name="media[]"', false);

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->content,
                'sort_order' => 0,
                'is_active' => 1,
                'media' => [UploadedFile::fake()->image('gallery.png')],
            ])
            ->assertRedirect(route('admin.resource.index', 'pages'));

        $page->refresh();
        $this->assertCount(1, $page->media);
        $mediaPath = $page->media[0];
        Storage::disk('public')->assertExists($mediaPath);

        $this->get(route('page.show', ['section' => 'profil', 'slug' => 'visi-misi']))
            ->assertOk()
            ->assertSee(asset('storage/'.$mediaPath), false);

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->content,
                'sort_order' => 0,
                'is_active' => 1,
                'remove_media' => [$mediaPath],
            ])
            ->assertRedirect(route('admin.resource.index', 'pages'));

        $page->refresh();
        $this->assertSame([], $page->media);
        Storage::disk('public')->assertMissing($mediaPath);
    }

    public function test_gallery_rejects_more_than_ten_images(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $page = Page::create([
            'section' => 'profil',
            'slug' => 'visi-misi',
            'title' => 'Visi dan Misi',
            'is_active' => true,
        ]);
        $images = [];
        for ($i = 1; $i <= 11; $i++) {
            $images[] = UploadedFile::fake()->image("gallery-$i.png");
        }

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'sort_order' => 0,
                'is_active' => 1,
                'media' => $images,
            ])
            ->assertSessionHasErrors('media');

        $this->assertEmpty($page->fresh()->media);
        $this->assertSame([], Storage::disk('public')->allFiles('uploads/pages'));
    }

    public function test_failed_page_update_keeps_removed_images_and_cleans_new_uploads(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $existingPath = 'uploads/pages/existing.png';
        Storage::disk('public')->put($existingPath, 'existing image');
        $page = Page::create([
            'section' => 'profil',
            'slug' => 'visi-misi',
            'title' => 'Visi dan Misi',
            'media' => [$existingPath],
            'is_active' => true,
        ]);
        Page::create([
            'section' => 'profil',
            'slug' => 'tentang-kami',
            'title' => 'Tentang Kami',
            'is_active' => true,
        ]);

        $this->from(route('admin.resource.edit', ['resource' => 'pages', 'id' => $page->id]))
            ->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => 'tentang-kami',
                'title' => $page->title,
                'sort_order' => 0,
                'is_active' => 1,
                'remove_media' => [$existingPath],
                'media' => [UploadedFile::fake()->image('replacement.png')],
            ])
            ->assertSessionHasErrors('db');

        $this->assertSame([$existingPath], $page->fresh()->media);
        Storage::disk('public')->assertExists($existingPath);
        $this->assertSame([$existingPath], Storage::disk('public')->allFiles('uploads/pages'));
    }

    public function test_media_can_be_positioned_inside_page_content_without_duplicate_gallery(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $mediaPath = 'uploads/pages/positioned.png';
        Storage::disk('public')->put($mediaPath, 'image');
        $page = Page::create([
            'section' => 'profil',
            'slug' => 'tupoksi',
            'title' => 'Tugas Pokok dan Fungsi',
            'media' => [$mediaPath],
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resource.edit', ['resource' => 'pages', 'id' => $page->id]))
            ->assertOk()
            ->assertSee('Sisipkan di isi')
            ->assertSee('data-insert-media="/storage/'.$mediaPath.'"', false);

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => '<p>Sebelum</p><p><img src="/storage/'.$mediaPath.'" alt="Foto" onerror="alert(1)"></p><p>Sesudah</p>',
                'sort_order' => 0,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.resource.index', 'pages'));

        $page->refresh();
        $this->assertStringContainsString('<img', $page->content);
        $this->assertStringContainsString($mediaPath, $page->content);
        $this->assertStringNotContainsString('onerror', $page->content);

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->content,
                'sort_order' => 0,
                'is_active' => 1,
                'remove_media' => [$mediaPath],
            ])
            ->assertSessionHasErrors('remove_media');

        $this->assertSame([$mediaPath], $page->fresh()->media);
        Storage::disk('public')->assertExists($mediaPath);

        $this->get(route('page.show', ['section' => 'profil', 'slug' => 'tupoksi']))
            ->assertOk()
            ->assertSee($mediaPath, false)
            ->assertDontSee('page-media-gallery', false);
    }

    public function test_page_cover_image_can_be_positioned_inline_without_duplicate_display(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $imagePath = 'uploads/pages/tupoksi.png';
        Storage::disk('public')->put($imagePath, 'image');
        $page = Page::create([
            'section' => 'profil',
            'slug' => 'tupoksi',
            'title' => 'Tugas Pokok dan Fungsi',
            'image' => $imagePath,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.resource.edit', ['resource' => 'pages', 'id' => $page->id]))
            ->assertOk()
            ->assertSee('data-insert-media="/storage/'.$imagePath.'"', false);

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => '<p>Sebelum</p><p><img src="/storage/'.$imagePath.'" alt="Tugas pokok"></p><p>Sesudah</p>',
                'sort_order' => 0,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.resource.index', 'pages'));

        $response = $this->get(route('page.show', ['section' => 'profil', 'slug' => 'tupoksi']))
            ->assertOk()
            ->assertSee($imagePath, false);
        $this->assertSame(1, substr_count($response->getContent(), '<img src="/storage/'.$imagePath.'"'));

        $this->actingAs($admin)
            ->put(route('admin.resource.update', ['resource' => 'pages', 'id' => $page->id]), [
                'section' => 'profil',
                'slug' => $page->slug,
                'title' => $page->title,
                'content' => $page->fresh()->content,
                'sort_order' => 0,
                'is_active' => 1,
                'remove_image' => 1,
            ])
            ->assertSessionHasErrors('image');

        $this->assertSame($imagePath, $page->fresh()->image);
        Storage::disk('public')->assertExists($imagePath);
    }
}
