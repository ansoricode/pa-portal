<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi login & panel ke IP/CIDR tertentu (ADMIN_ALLOWED_IPS). Kosong = tidak dibatasi.
 * Jika di belakang proxy/CDN, konfigurasikan trusted proxies agar $request->ip() benar.
 */
class RestrictAdminIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = array_values(array_filter(array_map('trim', explode(',', (string) config('portal.admin_allowed_ips')))));

        if ($allowed && ! IpUtils::checkIp((string) $request->ip(), $allowed)) {
            abort(404);
        }

        return $next($request);
    }
}
