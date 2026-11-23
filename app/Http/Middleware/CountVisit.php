<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Menghitung satu kunjungan per sesi per hari untuk statistik di footer. */
class CountVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->hasSession()) {
            $key = 'visited_' . now()->toDateString();
            if (! $request->session()->has($key)) {
                try {
                    Visit::hit();
                } catch (\Throwable $e) {
                    // abaikan: statistik tidak boleh mengganggu halaman
                }
                $request->session()->put($key, true);
            }
        }

        return $next($request);
    }
}
