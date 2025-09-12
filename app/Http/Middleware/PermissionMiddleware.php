<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('permission:users.manage') or multiple ->middleware('permission:users.manage,roles.manage')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $user = $request->user();

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
