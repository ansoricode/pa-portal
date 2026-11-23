<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\PageController;
use App\Models\ActivityLog;
use App\Models\ComplianceCheck;
use App\Models\News;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ComplianceController extends Controller
{
    public function index()
    {
        $this->ensureChecksExist();

        $checks = ComplianceCheck::whereIn('code', array_keys($this->indicators()))
            ->get()
            ->keyBy('code');

        $groups = collect(config('compliance.groups'))->map(function (array $group) use ($checks) {
            $group['items'] = collect($group['items'])->map(function (array $indicator, string $code) use ($checks) {
                $indicator['check'] = $checks->get($code);
                $indicator['absolute_url'] = $this->absoluteUrl($indicator['check']->target_url);

                return $indicator;
            });

            return $group;
        });

        $summary = [
            'total' => $checks->count(),
            'checked' => $checks->where('status', '!=', 'unchecked')->count(),
            'compliant' => $checks->where('status', 'compliant')->count(),
            'needs_improvement' => $checks->where('status', 'needs_improvement')->count(),
            'noncompliant' => $checks->where('status', 'noncompliant')->count(),
            'unchecked' => $checks->where('status', 'unchecked')->count(),
        ];

        return view('admin.compliance.index', [
            'groups' => $groups,
            'statuses' => config('compliance.statuses'),
            'linkStatuses' => config('compliance.link_statuses'),
            'summary' => $summary,
        ]);
    }

    public function update(Request $request, string $code)
    {
        $indicator = $this->indicators()[$code] ?? abort(404);
        $this->ensureChecksExist();

        $statusField = "status_$code";
        $urlField = "target_url_$code";
        $notesField = "notes_$code";
        $validated = $request->validate([
            $statusField => ['required', Rule::in(array_keys(config('compliance.statuses')))],
            $urlField => [
                'required',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $this->isValidUrl($value)) {
                        $fail('Masukkan URL http/https atau path lokal yang diawali dengan /.');
                    }
                },
            ],
            $notesField => ['nullable', 'string', 'max:2000'],
        ]);

        $status = $validated[$statusField];
        $notes = trim((string) ($validated[$notesField] ?? ''));
        if ($status === 'needs_improvement' && $notes === '') {
            throw ValidationException::withMessages([
                $notesField => 'Catatan perbaikan wajib diisi untuk status Perlu perbaikan.',
            ]);
        }

        $check = ComplianceCheck::where('code', $code)->firstOrFail();
        $before = $check->status;
        $check->update([
            'status' => $status,
            'target_url' => trim($validated[$urlField]),
            'notes' => $notes === '' ? null : $notes,
            'checked_at' => $status === 'unchecked' ? null : now(),
            'checked_by' => $status === 'unchecked' ? null : $request->user()->id,
        ]);

        ActivityLog::record('update', 'Memperbarui monitoring kepatuhan: '.$indicator['label'], $check, [
            'status' => ['dari' => $before, 'ke' => $status],
        ]);

        return redirect()
            ->to(route('admin.compliance.index').'#check-'.$code)
            ->with('ok', 'Status indikator berhasil disimpan.');
    }

    public function checkLink(Request $request, string $code)
    {
        $indicator = $this->indicators()[$code] ?? abort(404);
        $this->ensureChecksExist();

        $check = ComplianceCheck::where('code', $code)->firstOrFail();
        $result = $this->checkUrl($check->target_url, $request);
        $check->update([
            'link_status' => $result['status'],
            'link_http_status' => $result['http_status'],
            'link_message' => $result['message'],
            'link_checked_at' => now(),
            'link_checked_by' => $request->user()->id,
        ]);

        ActivityLog::record('update', 'Memeriksa tautan: '.$indicator['label'], $check, [
            'link_status' => $result['status'],
            'http_status' => $result['http_status'],
        ]);

        return redirect()
            ->to(route('admin.compliance.index').'#check-'.$code)
            ->with('ok', 'Hasil cek tautan: '.$result['message']);
    }

    protected function indicators(): array
    {
        return collect(config('compliance.groups'))
            ->flatMap(fn (array $group) => $group['items'])
            ->all();
    }

    protected function ensureChecksExist(): void
    {
        $rows = [];
        foreach ($this->indicators() as $code => $indicator) {
            $targetUrl = $indicator['url'];
            foreach ($indicator['url_settings'] ?? [] as $settingKey) {
                $targetUrl = Setting::get($settingKey) ?: $targetUrl;
                if ($targetUrl !== $indicator['url']) {
                    break;
                }
            }

            $rows[] = [
                'code' => $code,
                'status' => 'unchecked',
                'target_url' => $targetUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('compliance_checks')->insertOrIgnore($rows);
    }

    protected function absoluteUrl(string $value): string
    {
        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return url($value);
        }

        return $this->isValidUrl($value) ? $value : url('/');
    }

    protected function isValidUrl(mixed $value): bool
    {
        if (! is_string($value) || trim($value) === '') {
            return false;
        }

        if (str_starts_with($value, '/')) {
            return ! str_starts_with($value, '//');
        }

        $parts = parse_url($value);

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array($parts['scheme'] ?? null, ['http', 'https'], true)
            && ! isset($parts['user'])
            && ! isset($parts['pass']);
    }

    protected function checkUrl(string $url, Request $request): array
    {
        if (! $this->isValidUrl($url)) {
            return $this->linkResult('blocked', null, 'URL tidak valid atau protokol tidak diizinkan.');
        }

        if (str_starts_with($url, '/') || $this->isSameOrigin($url, $request)) {
            return $this->checkLocalUrl($url);
        }

        return $this->checkExternalUrl($url);
    }

    protected function checkLocalUrl(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, '/storage/')) {
            $relativePath = rawurldecode(substr($path, strlen('/storage/')));
            if (str_contains($relativePath, '..') || str_contains($relativePath, '\\')) {
                return $this->linkResult('blocked', null, 'Path file tidak diizinkan.');
            }

            return Storage::disk('public')->exists($relativePath)
                ? $this->linkResult('available', 200, 'File lokal ditemukan.')
                : $this->linkResult('broken', 404, 'File lokal tidak ditemukan.');
        }

        try {
            $route = Route::getRoutes()->match(Request::create($path, 'GET'));
        } catch (ResourceNotFoundException|MethodNotAllowedException) {
            return $this->linkResult('broken', 404, 'Rute lokal tidak ditemukan.');
        }

        $parameters = $route->parameters();
        if ($route->getControllerClass() === PageController::class) {
            $page = Page::where('section', $parameters['section'] ?? '')
                ->where('is_active', true)
                ->when($parameters['slug'] ?? null, fn ($query, $slug) => $query->where('slug', $slug))
                ->exists();

            return $page
                ? $this->linkResult('available', null, 'Halaman aktif ditemukan.')
                : $this->linkResult('broken', 404, 'Halaman tidak ada atau tidak aktif.');
        }

        if ($route->getControllerClass() === NewsController::class) {
            if ($route->getActionMethod() !== 'show') {
                return $this->linkResult('available', null, 'Rute berita ditemukan.');
            }

            $news = News::where('slug', $parameters['slug'] ?? '')
                ->where('is_published', true)
                ->exists();

            return $news
                ? $this->linkResult('available', null, 'Berita terbit ditemukan.')
                : $this->linkResult('broken', 404, 'Berita tidak ada atau belum terbit.');
        }

        if (in_array('auth', $route->gatherMiddleware(), true)) {
            return $this->linkResult('unknown', 401, 'Rute memerlukan login dan bukan tautan publik.');
        }

        return $this->linkResult('available', null, 'Rute lokal ditemukan.');
    }

    protected function checkExternalUrl(string $url): array
    {
        $parts = parse_url($url);
        $host = strtolower(rtrim($parts['host'] ?? '', '.'));
        $scheme = strtolower($parts['scheme'] ?? '');
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if (! $this->isConfiguredExternalHost($host)) {
            return $this->linkResult('blocked', null, 'Domain eksternal belum diizinkan di Pengaturan Situs > Tautan Sistem.');
        }

        if ($port !== ($scheme === 'https' ? 443 : 80) || filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->linkResult('blocked', null, 'Port khusus atau alamat IP eksternal tidak diizinkan.');
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA);
        $addresses = collect($records ?: [])
            ->flatMap(fn ($record) => array_filter([$record['ip'] ?? null, $record['ipv6'] ?? null]))
            ->unique()
            ->values();

        if ($addresses->isEmpty()) {
            return $this->linkResult('unknown', null, 'Domain tidak dapat di-resolve; periksa manual.');
        }
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return $this->linkResult('blocked', null, 'Domain mengarah ke alamat IP privat atau khusus.');
            }
        }

        try {
            $response = Http::connectTimeout(2)
                ->timeout(5)
                ->withoutRedirecting()
                ->head($url);
        } catch (ConnectionException) {
            return $this->linkResult('unknown', null, 'Koneksi gagal atau melewati batas waktu; periksa manual.');
        }

        $status = $response->status();
        if ($status >= 200 && $status < 300) {
            return $this->linkResult('available', $status, "Server merespons HTTP $status.");
        }
        if ($status >= 300 && $status < 400) {
            return $this->linkResult('redirect', $status, "Server mengarahkan ulang (HTTP $status); tujuan tidak diikuti.");
        }
        if (in_array($status, [401, 403, 405, 429], true)) {
            return $this->linkResult('unknown', $status, "Server membatasi pemeriksaan otomatis (HTTP $status); periksa manual.");
        }

        return $this->linkResult('broken', $status, "Server merespons HTTP $status.");
    }

    protected function isSameOrigin(string $url, Request $request): bool
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        $requestScheme = strtolower($request->getScheme());
        $requestPort = $request->getPort() ?: ($requestScheme === 'https' ? 443 : 80);

        return strtolower($parts['host'] ?? '') === strtolower($request->getHost())
            && $scheme === $requestScheme
            && $port === $requestPort;
    }

    protected function isConfiguredExternalHost(string $host): bool
    {
        foreach (['sipp_url', 'ecourt_url', 'putusan_url', 'putusan_satker_url'] as $settingKey) {
            $configuredUrl = Setting::get($settingKey);
            $configuredHost = $configuredUrl ? parse_url($configuredUrl, PHP_URL_HOST) : null;
            if ($configuredHost && strtolower(rtrim($configuredHost, '.')) === $host) {
                return true;
            }
        }

        return false;
    }

    protected function linkResult(string $status, ?int $httpStatus, string $message): array
    {
        return [
            'status' => $status,
            'http_status' => $httpStatus,
            'message' => $message,
        ];
    }
}
