<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|between:1,4',
            'comment' => 'nullable|string|max:1000',
        ]);

        SurveyResponse::create($data + ['ip_address' => $request->ip()]);

        return back()->with('survey_ok', 'Terima kasih! Partisipasi Anda telah tersimpan untuk peningkatan mutu layanan peradilan.');
    }
}
