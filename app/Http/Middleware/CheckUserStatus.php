<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user()->fresh();

            $allowedRoutes = ['pending', 'rejected', 'logout'];

            if ($user->status === 'rejected' && !$request->routeIs(...$allowedRoutes)) {
                return redirect()->route('rejected');
            }

            if ($user->status === 'pending' && !$request->routeIs(...$allowedRoutes)) {
                return redirect()->route('pending');
            }
        }

        return $next($request);
    }
}