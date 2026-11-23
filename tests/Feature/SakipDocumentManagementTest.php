<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SakipDocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_manage_sakip_documents_from_a_scoped_menu_and_page_shortcut(): void
    {
        config(['portal.require_2fa' => false]);

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $page = Page::create([
            'section' => 'transparansi',
            'slug' => 'sakip',
            'title' => 'Dokumen SAKIP',
            'is_active' => true,
        ]);
        $sakipDocument = Document::create([
            'category' => 'sakip',
            'title' => 'Rencana Strategis',
            'period' => '2022–2026',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $financeDocument = Document::create([
            'category' => 'keuangan',
            'title' => 'Dokumen Keuangan Privat',
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.resource.index', 'sakip_documents'))
            ->assertOk()
            ->assertSee($sakipDocument->title)
            ->assertDontSee($financeDocument->title);

        $this->actingAs($editor)
            ->get(route('admin.resource.edit', ['resource' => 'pages', 'id' => $page->id]))
            ->assertOk()
            ->assertSee('Kelola Dokumen SAKIP')
            ->assertSee(route('admin.resource.index', 'sakip_documents'));

        $this->actingAs($editor)
            ->get(route('admin.resource.index', 'documents'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->post(route('admin.resource.store', 'sakip_documents'), [
                'title' => 'Rencana Aksi Kinerja',
                'period' => '2027–2031',
                'category' => 'keuangan',
                'external_url' => 'https://example.test/rencana-aksi.pdf',
                'sort_order' => 2,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.resource.index', 'sakip_documents'));

        $this->assertDatabaseHas('documents', [
            'category' => 'sakip',
            'title' => 'Rencana Aksi Kinerja',
            'period' => '2027–2031',
            'external_url' => 'https://example.test/rencana-aksi.pdf',
        ]);

        $this->get(route('page.show', ['section' => 'transparansi', 'slug' => 'sakip']))
            ->assertOk()
            ->assertSee('Periode: 2022–2026')
            ->assertSee('Periode: 2027–2031')
            ->assertSee('https://example.test/rencana-aksi.pdf')
            ->assertSee('Segera tersedia');
    }

    public function test_editor_cannot_access_or_delete_documents_outside_the_sakip_scope(): void
    {
        config(['portal.require_2fa' => false]);

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $financeDocument = Document::create([
            'category' => 'keuangan',
            'title' => 'Dokumen Keuangan',
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.resource.edit', ['resource' => 'sakip_documents', 'id' => $financeDocument->id]))
            ->assertNotFound();

        $this->actingAs($editor)
            ->delete(route('admin.resource.destroy', ['resource' => 'sakip_documents', 'id' => $financeDocument->id]))
            ->assertNotFound();

        $this->assertDatabaseHas('documents', ['id' => $financeDocument->id, 'category' => 'keuangan']);
    }

    public function test_sakip_document_upload_is_available_on_the_public_page(): void
    {
        config(['portal.require_2fa' => false]);
        Storage::fake('public');

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        Page::create([
            'section' => 'transparansi',
            'slug' => 'sakip',
            'title' => 'Dokumen SAKIP',
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->post(route('admin.resource.store', 'sakip_documents'), [
                'title' => 'Renstra dengan Berkas',
                'period' => '2022–2026',
                'file_path' => UploadedFile::fake()->create('renstra.pdf', 100, 'application/pdf'),
                'sort_order' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.resource.index', 'sakip_documents'));

        $document = Document::where('title', 'Renstra dengan Berkas')->firstOrFail();
        Storage::disk('public')->assertExists($document->file_path);

        $this->get(route('page.show', ['section' => 'transparansi', 'slug' => 'sakip']))
            ->assertOk()
            ->assertSee('Renstra dengan Berkas')
            ->assertSee(asset('storage/'.$document->file_path), false)
            ->assertDontSee('Segera tersedia');
    }

    public function test_sakip_document_requires_a_title_and_period(): void
    {
        config(['portal.require_2fa' => false]);

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->actingAs($editor)
            ->post(route('admin.resource.store', 'sakip_documents'), [])
            ->assertSessionHasErrors(['title', 'period']);

        $this->assertDatabaseCount('documents', 0);
    }
}
