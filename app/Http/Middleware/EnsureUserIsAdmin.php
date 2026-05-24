<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Only allow users with role 'admin' to pass through.
     * Backend team: update the role check to match your User model field.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: sesuaikan dengan field role di model User nanti
        if (!$request->user() || $request->user()->role !== 'admin') {
            // kalau bukan admin, redirect ke dashboard biasa
            return redirect()->route('dashboard')->with('error', 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
