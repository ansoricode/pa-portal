<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengelola footer.');

        return view('admin.footer', [
            'footerLinks' => $this->decode('footer_links', $this->defaultLinks()),
            'ecosystemLinks' => $this->decode('footer_ecosystem', $this->defaultEcosystem()),
            'socialLinks' => $this->decode('footer_social', $this->defaultSocialLinks()),
            'footerHours' => Setting::get('footer_hours', ''),
            'footerCopyright' => Setting::get('footer_copyright', ''),
            'footerLinksTitle' => Setting::get('footer_links_title', 'Tautan Footer'),
            'footerEcosystemTitle' => Setting::get('footer_ecosystem_title', 'Tautan Ekosistem Peradilan'),
            'footerStatsTitle' => Setting::get('footer_stats_title', 'Statistik Kunjungan Website'),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengelola footer.');

        $request->validate([
            'footer_hours' => 'nullable|string|max:1000',
            'footer_copyright' => 'nullable|string|max:500',
            'footer_links_title' => 'nullable|string|max:150',
            'footer_ecosystem_title' => 'nullable|string|max:150',
            'footer_stats_title' => 'nullable|string|max:150',
            'footer_links' => 'nullable|array|max:30',
            'footer_links.*.name' => 'required_with:footer_links|string|max:150',
            'footer_links.*.url' => 'required_with:footer_links|url:http,https|max:2048',
            'footer_links.*.description' => 'nullable|string|max:300',
            'footer_links.*.icon' => 'nullable|string|max:100',
            'footer_links.*.sort_order' => 'nullable|integer|min:0|max:999',
            'footer_links.*.icon_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'ecosystem_links' => 'nullable|array|max:30',
            'ecosystem_links.*.name' => 'required_with:ecosystem_links|string|max:150',
            'ecosystem_links.*.url' => 'required_with:ecosystem_links|url:http,https|max:2048',
            'ecosystem_links.*.icon' => 'nullable|string|max:100',
            'ecosystem_links.*.sort_order' => 'nullable|integer|min:0|max:999',
            'ecosystem_links.*.logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'social_links' => 'nullable|array|max:30',
            'social_links.*.name' => 'required_with:social_links|string|max:100',
            'social_links.*.url' => 'required_with:social_links|url:http,https|max:2048',
            'social_links.*.icon' => 'nullable|string|max:100',
            'social_links.*.sort_order' => 'nullable|integer|min:0|max:999',
            'facebook_url' => 'nullable|url:http,https|max:2048',
            'instagram_url' => 'nullable|url:http,https|max:2048',
            'youtube_url' => 'nullable|url:http,https|max:2048',
        ]);

        $footerLinks = [];
        foreach ((array) $request->input('footer_links', []) as $index => $link) {
            $icon = trim((string) ($link['icon'] ?? ''));
            $existingIcon = trim((string) ($link['icon_existing'] ?? ''));
            $file = $request->file("footer_links.$index.icon_file");
            if ($file) {
                if ($existingIcon && str_starts_with($existingIcon, 'uploads/footer/')) {
                    Storage::disk('public')->delete($existingIcon);
                }
                $icon = $file->store('uploads/footer', 'public');
            } elseif ($icon === '' && $existingIcon) {
                $icon = $existingIcon;
            }

            $footerLinks[] = [
                'name' => trim((string) ($link['name'] ?? '')),
                'url' => trim((string) ($link['url'] ?? '')),
                'description' => trim((string) ($link['description'] ?? '')),
                'icon' => $icon,
                'sort_order' => (int) ($link['sort_order'] ?? 0),
                'is_active' => isset($link['is_active']) && (string) $link['is_active'] === '1',
            ];
        }

        $ecosystemLinks = [];
        foreach ((array) $request->input('ecosystem_links', []) as $index => $link) {
            $logo = trim((string) ($link['logo_existing'] ?? ''));
            $icon = trim((string) ($link['icon'] ?? ''));
            $file = $request->file("ecosystem_links.$index.logo_file");
            if ($file) {
                if ($logo) {
                    Storage::disk('public')->delete($logo);
                }
                $logo = $file->store('uploads/footer', 'public');
            } elseif ($icon !== '') {
                $logo = '';
            } elseif (! empty($link['logo_existing'])) {
                $logo = trim((string) $link['logo_existing']);
            }

            $ecosystemLinks[] = [
                'name' => trim((string) ($link['name'] ?? '')),
                'url' => trim((string) ($link['url'] ?? '')),
                'logo' => $logo,
                'icon' => $icon,
                'sort_order' => (int) ($link['sort_order'] ?? 0),
                'is_active' => isset($link['is_active']) && (string) $link['is_active'] === '1',
            ];
        }

        $socialLinks = [];
        foreach ((array) $request->input('social_links', []) as $link) {
            $socialLinks[] = [
                'name' => trim((string) ($link['name'] ?? '')),
                'url' => trim((string) ($link['url'] ?? '')),
                'icon' => trim((string) ($link['icon'] ?? '')),
                'sort_order' => (int) ($link['sort_order'] ?? 0),
                'is_active' => isset($link['is_active']) && (string) $link['is_active'] === '1',
            ];
        }

        Setting::put('footer_links', json_encode($footerLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Setting::put('footer_ecosystem', json_encode($ecosystemLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Setting::put('footer_social', json_encode($socialLinks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Setting::put('footer_hours', $request->input('footer_hours'));
        Setting::put('footer_copyright', $request->input('footer_copyright'));
        Setting::put('footer_links_title', trim((string) $request->input('footer_links_title')) ?: 'Tautan Footer');
        Setting::put('footer_ecosystem_title', trim((string) $request->input('footer_ecosystem_title')) ?: 'Tautan Ekosistem Peradilan');
        Setting::put('footer_stats_title', trim((string) $request->input('footer_stats_title')) ?: 'Statistik Kunjungan Website');
        foreach (['facebook', 'instagram', 'youtube'] as $key) {
            $matchingSocial = collect($socialLinks)->first(
                fn (array $social) => strtolower($social['name']) === $key && $social['is_active']
            );
            Setting::put("{$key}_url", is_array($matchingSocial) ? $matchingSocial['url'] : null);
        }

        ActivityLog::record('settings_updated', 'Memperbarui pengaturan footer', null, [
            'jumlah_tautan' => count($footerLinks),
            'jumlah_ekosistem' => count($ecosystemLinks),
        ]);

        return back()->with('ok', 'Pengaturan footer berhasil disimpan.');
    }

    private function decode(string $key, array $defaults): array
    {
        $decoded = json_decode((string) Setting::get($key, ''), true);

        return is_array($decoded) ? $decoded : $defaults;
    }

    private function defaultLinks(): array
    {
        return [];
    }

    private function defaultEcosystem(): array
    {
        return [
            ['name' => 'Mahkamah Agung RI', 'url' => 'https://www.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 10, 'is_active' => true],
            ['name' => 'Ditjen Badan Peradilan Agama', 'url' => 'https://badilag.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 20, 'is_active' => true],
            ['name' => 'JDIH Mahkamah Agung RI', 'url' => 'https://jdih.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 30, 'is_active' => true],
            ['name' => 'SIMTEPA Badilag', 'url' => 'https://simtepa.badilag.net', 'logo' => '', 'sort_order' => 40, 'is_active' => true],
        ];
    }

    private function defaultSocialLinks(): array
    {
        $defaults = [];
        foreach ([
            'facebook' => ['name' => 'Facebook', 'icon' => 'bi-facebook', 'sort_order' => 10],
            'instagram' => ['name' => 'Instagram', 'icon' => 'bi-instagram', 'sort_order' => 20],
            'youtube' => ['name' => 'YouTube', 'icon' => 'bi-youtube', 'sort_order' => 30],
        ] as $key => $meta) {
            $url = trim((string) Setting::get("{$key}_url", ''));
            if ($url !== '') {
                $defaults[] = [
                    'name' => $meta['name'],
                    'url' => $url,
                    'icon' => $meta['icon'],
                    'sort_order' => $meta['sort_order'],
                    'is_active' => true,
                ];
            }
        }

        return $defaults;
    }
}
