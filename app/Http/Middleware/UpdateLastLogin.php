<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stamp last_login_at once per session so the admin user-detail page
 * shows an accurate "Terakhir Aktif" value.
 */
class UpdateLastLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only write once per session to avoid a DB hit on every request
        if ($user && !$request->session()->has('last_login_stamped')) {
            $user->updateQuietly(['last_login_at' => now()]);
            $request->session()->put('last_login_stamped', true);
        }

        return $next($request);
    }
}