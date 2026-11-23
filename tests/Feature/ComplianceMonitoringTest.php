<?php

namespace Tests\Feature;

use App\Models\ComplianceCheck;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ComplianceMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_menu_lists_copyable_indicators_and_default_urls(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.compliance.index'));

        $response->assertOk()
            ->assertSee('Monitoring Kepatuhan Website')
            ->assertSee('Visi dan Misi Pengadilan')
            ->assertSee('Sistem Informasi Penelusuran Perkara (SIPP)')
            ->assertSee('data-copy-url="check-url-tupoksi"', false)
            ->assertSee('Cek tautan')
            ->assertSee('href="'.url('/profil/tupoksi').'"', false);

        $this->assertSame(41, DB::table('compliance_checks')->count());
    }

    public function test_improvement_status_requires_notes_and_saves_status_and_url(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->actingAs($admin)->get(route('admin.compliance.index'))->assertOk();

        $this->put(route('admin.compliance.update', 'tupoksi'), [
            'status_tupoksi' => 'needs_improvement',
            'target_url_tupoksi' => '/profil/tupoksi',
            'notes_tupoksi' => '',
        ])->assertSessionHasErrors('notes_tupoksi');

        $this->put(route('admin.compliance.update', 'tupoksi'), [
            'status_tupoksi' => 'needs_improvement',
            'target_url_tupoksi' => '/profil/tupoksi',
            'notes_tupoksi' => 'Konten fungsi pengadilan perlu dilengkapi.',
        ])->assertRedirect(route('admin.compliance.index').'#check-tupoksi');

        $this->assertDatabaseHas('compliance_checks', [
            'code' => 'tupoksi',
            'status' => 'needs_improvement',
            'target_url' => '/profil/tupoksi',
            'notes' => 'Konten fungsi pengadilan perlu dilengkapi.',
            'checked_by' => $admin->id,
        ]);
        $this->assertNotNull(ComplianceCheck::where('code', 'tupoksi')->value('checked_at'));
    }

    public function test_monitoring_rejects_unsafe_urls(): void
    {
        config(['portal.require_2fa' => false]);
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->actingAs($admin)->get(route('admin.compliance.index'))->assertOk();

        $this->put(route('admin.compliance.update', 'tupoksi'), [
            'status_tupoksi' => 'compliant',
            'target_url_tupoksi' => 'javascript:alert(1)',
            'notes_tupoksi' => '',
        ])->assertSessionHasErrors('target_url_tupoksi');
    }

    public function test_local_pages_and_missing_pages_are_checked_without_network_requests(): void
    {
        config(['portal.require_2fa' => false]);
        Http::fake();
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
        $this->actingAs($admin)->get(route('admin.compliance.index'))->assertOk();

        DB::table('compliance_checks')->where('code', 'tupoksi')->update([
            'target_url' => '/profil/tupoksi',
        ]);
        $this->post(route('admin.compliance.check-link', 'tupoksi'))
            ->assertRedirect(route('admin.compliance.index').'#check-tupoksi');
        $this->assertDatabaseHas('compliance_checks', [
            'code' => 'tupoksi',
            'link_status' => 'available',
            'link_message' => 'Halaman aktif ditemukan.',
        ]);

        DB::table('compliance_checks')->where('code', 'tupoksi')->update([
            'target_url' => '/profil/halaman-tidak-ada',
        ]);
        $this->post(route('admin.compliance.check-link', 'tupoksi'))
            ->assertRedirect(route('admin.compliance.index').'#check-tupoksi');
        $this->assertDatabaseHas('compliance_checks', [
            'code' => 'tupoksi',
            'link_status' => 'broken',
            'link_http_status' => 404,
        ]);

        Http::assertNothingSent();
    }

    public function test_unconfigured_external_hosts_are_blocked_from_server_checks(): void
    {
        config(['portal.require_2fa' => false]);
        Http::fake();
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->actingAs($admin)->get(route('admin.compliance.index'))->assertOk();

        DB::table('compliance_checks')->where('code', 'tupoksi')->update([
            'target_url' => 'https://example.com/',
        ]);
        $this->post(route('admin.compliance.check-link', 'tupoksi'))
            ->assertRedirect(route('admin.compliance.index').'#check-tupoksi');

        $this->assertDatabaseHas('compliance_checks', [
            'code' => 'tupoksi',
            'link_status' => 'blocked',
            'link_message' => 'Domain eksternal belum diizinkan di Pengaturan Situs > Tautan Sistem.',
        ]);
        Http::assertNothingSent();
    }
}
