<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponse;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $resources = collect(config('admin_resources'))->filter(fn ($def, $key) => $user->canManage($key));
        $counts = $resources->map(fn ($def) => $def['model']::count());

        return view('admin.dashboard', [
            'resources'   => $resources,
            'counts'      => $counts,
            'visits'      => Visit::stats(),
            'surveyAvg'   => round((float) SurveyResponse::avg('rating'), 2),
            'surveyCount' => SurveyResponse::count(),
        ]);
    }
}
