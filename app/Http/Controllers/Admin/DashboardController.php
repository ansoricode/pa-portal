<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\News;
use App\Models\SurveyResponse;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $resources = collect(config('admin_resources'))->filter(fn ($def, $key) => $user->canManage($key));
        $counts = $resources->map(fn ($def) => $def['model']::count());
        $visitTrend = Visit::dailyTrend();
        $visitTrendMax = max(1, max(array_column($visitTrend, 'count')));
        $visitChart = collect($visitTrend)->values()->map(function ($day, $index) use ($visitTrendMax) {
            $height = $day['count'] > 0
                ? max(4, (int) round(($day['count'] / $visitTrendMax) * 150))
                : 0;

            return $day + [
                'x' => 56 + ($index * 27),
                'y' => 205 - $height,
                'height' => $height,
                'show_label' => in_array($index, [0, 7, 14, 21, 29], true),
            ];
        })->all();
        $popularNews = $resources->has('news')
            ? News::published()->orderByDesc('view_count')->orderByDesc('published_at')->limit(5)
                ->get(['title', 'category', 'view_count'])
            : collect();
        $popularAnnouncements = $resources->has('announcements')
            ? Announcement::where('is_active', true)->whereNotNull('url')->where('url', '!=', '')
                ->orderByDesc('view_count')->orderByDesc('published_at')
                ->limit(5)->get(['title', 'badge', 'view_count'])
            : collect();

        return view('admin.dashboard', [
            'resources' => $resources,
            'counts' => $counts,
            'visits' => Visit::stats(),
            'visitChart' => $visitChart,
            'visitTrendMax' => $visitTrendMax,
            'popularNews' => $popularNews,
            'popularAnnouncements' => $popularAnnouncements,
            'surveyAvg' => round((float) SurveyResponse::avg('rating'), 2),
            'surveyCount' => SurveyResponse::count(),
        ]);
    }
}
