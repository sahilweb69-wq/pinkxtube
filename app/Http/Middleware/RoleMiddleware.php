<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin') or multiple ->middleware('role:admin,manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = $request->user();

        if (empty($roles)) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403);
    }
}
