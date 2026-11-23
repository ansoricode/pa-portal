<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CourtSchedule;
use App\Models\Document;
use App\Models\Jurisdiction;
use App\Models\Official;
use App\Models\Page;
use App\Models\PanggilanGhaib;
use App\Models\SurveyIndex;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(Request $request, string $section, ?string $slug = null)
    {
        $sections = config('portal.sections');
        abort_unless(isset($sections[$section]), 404);

        $pages = Page::where('section', $section)->where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')->get();

        $page = $slug ? $pages->firstWhere('slug', $slug) : $pages->first();
        abort_unless($page, 404);

        return view('public.page', array_merge($this->extra($request, $section, $page->slug), [
            'section'    => $section,
            'sectionCfg' => $sections[$section],
            'pages'      => $pages,
            'page'       => $page,
        ]));
    }

    /** Data tambahan di bawah isi halaman, sesuai slug. Daftar yang bisa membesar dipaginasi + dapat dicari. */
    protected function extra(Request $request, string $section, string $slug): array
    {
        return match ("$section/$slug") {
            'profil/yurisdiksi'       => ['jurisdictions' => Jurisdiction::orderBy('sort_order')->orderBy('id')->get()],
            'profil/pejabat'          => $this->officials($request),
            'layanan-hukum/jadwal'    => $this->schedules($request),
            'layanan-hukum/panjar'    => $this->documents($request, 'panjar'),
            'layanan-hukum/prosedur'  => $this->documents($request, 'prosedur', 12),
            'layanan-hukum/ghaib'     => $this->summons($request),
            'layanan-hukum/statistik' => $this->documents($request, 'statistik'),
            'transparansi/sakip'      => $this->documents($request, 'sakip'),
            'transparansi/keuangan'   => $this->documents($request, 'keuangan'),
            'transparansi/pbj'        => $this->documents($request, 'pbj'),
            'transparansi/lhkpn'      => $this->lhkpn($request),
            'layanan-publik/ppid'      => $this->documents($request, 'ppid'),
            'layanan-publik/pengaduan' => $this->documents($request, 'pengaduan'),
            'layanan-publik/survei'    => ['indices' => SurveyIndex::orderBy('sort_order')->get()],
            default => [],
        };
    }

    /** Pola LIKE aman dari kata kunci ?q= (null jika kosong). */
    protected function like(Request $request): ?string
    {
        $q = trim((string) $request->query('q', ''));

        return $q === '' ? null : '%' . addcslashes($q, '%_\\') . '%';
    }

    protected function documents(Request $request, string $category, int $perPage = 10): array
    {
        $like = $this->like($request);
        $year = (int) $request->query('tahun');

        $years = Document::where('category', $category)->where('is_active', true)
            ->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');

        $documents = Document::category($category)
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like)))
            ->when($year > 0, fn ($q) => $q->where('year', $year))
            ->paginate($perPage)
            ->withQueryString();

        return ['documents' => $documents, 'years' => $years];
    }

    protected function schedules(Request $request): array
    {
        $like = $this->like($request);
        $date = (string) $request->query('tanggal', '');
        $validDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false ? $date : null;

        $schedules = CourtSchedule::query()
            ->when($validDate,
                fn ($q) => $q->whereDate('hearing_date', $validDate),
                fn ($q) => $q->whereDate('hearing_date', '>=', today()))
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('case_number', 'like', $like)
                ->orWhere('agenda', 'like', $like)->orWhere('courtroom', 'like', $like)))
            ->orderBy('hearing_date')->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return ['schedules' => $schedules, 'showDate' => true];
    }

    protected function summons(Request $request): array
    {
        $like = $this->like($request);

        $summons = PanggilanGhaib::query()
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('party_name', 'like', $like)->orWhere('case_number', 'like', $like)))
            ->orderByDesc('hearing_date')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return ['summons' => $summons];
    }

    protected function officials(Request $request): array
    {
        $like = $this->like($request);

        $officials = Official::where('is_active', true)
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('position', 'like', $like)))
            ->orderByRaw("CASE category WHEN 'pejabat' THEN 1 WHEN 'pegawai' THEN 2 ELSE 3 END")
            ->orderBy('sort_order')->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return ['officials' => $officials];
    }

    protected function lhkpn(Request $request): array
    {
        $like = $this->like($request);

        $officials = Official::where('is_lhkpn', true)->where('is_active', true)
            ->when($like, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('position', 'like', $like)))
            ->with(['lhkpnReports' => fn ($q) => $q->orderByDesc('year')])
            ->orderBy('sort_order')->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return ['officials' => $officials];
    }
}
