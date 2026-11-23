<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\News;
use App\Models\Setting;
use App\Models\Shortcut;
use App\Models\Slider;
use App\Models\SurveyIndex;

class HomeController extends Controller
{
    public function index()
    {
        $shortcuts = Shortcut::where('is_active', true)->orderBy('sort_order')->get();
        $sippUrl = Setting::get('sipp_url');
        $sippShortcut = $shortcuts->firstWhere('title', 'SIPP Satker');

        if ($sippShortcut && filled($sippUrl)) {
            $sippShortcut->url = $sippUrl;
        }

        return view('public.home', [
            'sliders'       => Slider::where('is_active', true)->orderBy('sort_order')->limit(5)->get(),
            'shortcuts'     => $shortcuts,
            'announcements' => Announcement::where('is_active', true)->orderByDesc('published_at')->orderByDesc('id')->limit(4)->get(),
            'latestNews'    => News::published()->orderByDesc('published_at')->limit(2)->get(),
            'indices'       => SurveyIndex::orderBy('sort_order')->get(),
        ]);
    }
}
