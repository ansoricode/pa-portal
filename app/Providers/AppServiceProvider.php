<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tampilan pagination berbahasa Indonesia (resources/views/vendor/pagination/portal.blade.php)
        Paginator::defaultView('vendor.pagination.portal');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
        Carbon::setLocale('id');

        // Kebijakan kata sandi: min. 12 karakter, huruf besar+kecil, angka, simbol
        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers()->symbols());

        // $setting('kunci', 'default') tersedia di semua view
        View::share('setting', fn (string $key, $default = null) => Setting::get($key, $default));
        $legacySocialLinks = array_values(array_filter([
            Setting::get('facebook_url') ? ['name' => 'Facebook', 'url' => Setting::get('facebook_url'), 'icon' => 'bi-facebook', 'sort_order' => 10, 'is_active' => true] : null,
            Setting::get('instagram_url') ? ['name' => 'Instagram', 'url' => Setting::get('instagram_url'), 'icon' => 'bi-instagram', 'sort_order' => 20, 'is_active' => true] : null,
            Setting::get('youtube_url') ? ['name' => 'YouTube', 'url' => Setting::get('youtube_url'), 'icon' => 'bi-youtube', 'sort_order' => 30, 'is_active' => true] : null,
        ]));
        $storedSocialLinks = json_decode((string) Setting::get('footer_social', ''), true);
        $footerSocial = is_array($storedSocialLinks) ? $storedSocialLinks : $legacySocialLinks;
        $footerSocial = collect($footerSocial)
            ->filter(fn ($item) => ($item['is_active'] ?? true) && ! empty($item['name']) && ! empty($item['url']))
            ->sortBy(fn ($item) => (int) ($item['sort_order'] ?? 0))
            ->values()
            ->all();
        View::share('footerSocial', $footerSocial);

        // Menu navigasi dinamis + statistik kunjungan untuk layout publik
        View::composer('layouts.app', function ($view) {
            $decodeFooter = function (string $key, array $defaults = []) {
                $value = json_decode((string) Setting::get($key, ''), true);
                if (! is_array($value)) {
                    return $defaults;
                }

                return collect($value)
                    ->filter(fn ($item) => ($item['is_active'] ?? true) && ! empty($item['name']) && ! empty($item['url']))
                    ->sortBy(fn ($item) => (int) ($item['sort_order'] ?? 0))
                    ->values()
                    ->all();
            };

            $view->with([
                'navPages' => Page::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()->groupBy('section'),
                'visitStats' => Visit::stats(),
                'footerLinks' => $decodeFooter('footer_links'),
                'footerEcosystem' => $decodeFooter('footer_ecosystem', [
                    ['name' => 'Mahkamah Agung RI', 'url' => 'https://www.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 10, 'is_active' => true],
                    ['name' => 'Ditjen Badan Peradilan Agama', 'url' => 'https://badilag.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 20, 'is_active' => true],
                    ['name' => 'JDIH Mahkamah Agung RI', 'url' => 'https://jdih.mahkamahagung.go.id', 'logo' => '', 'sort_order' => 30, 'is_active' => true],
                    ['name' => 'SIMTEPA Badilag', 'url' => 'https://simtepa.badilag.net', 'logo' => '', 'sort_order' => 40, 'is_active' => true],
                ]),
                'footerSocial' => $decodeFooter('footer_social', array_values(array_filter([
                    Setting::get('facebook_url') ? ['name' => 'Facebook', 'url' => Setting::get('facebook_url'), 'icon' => 'bi-facebook', 'sort_order' => 10, 'is_active' => true] : null,
                    Setting::get('instagram_url') ? ['name' => 'Instagram', 'url' => Setting::get('instagram_url'), 'icon' => 'bi-instagram', 'sort_order' => 20, 'is_active' => true] : null,
                    Setting::get('youtube_url') ? ['name' => 'YouTube', 'url' => Setting::get('youtube_url'), 'icon' => 'bi-youtube', 'sort_order' => 30, 'is_active' => true] : null,
                ]))),
            ]);
        });
    }
}
