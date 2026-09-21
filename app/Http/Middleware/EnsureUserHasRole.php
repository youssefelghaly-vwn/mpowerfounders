<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered as the 'role' middleware alias.
 *
 * With specific roles:  ->middleware('role:admin') or ->middleware('role:admin,editor')
 * With no roles at all: ->middleware('role')  — passes anyone who has been
 *   assigned at least one role, regardless of which. This is what gates the
 *   admin panel's entry point (the dashboard) — individual sections inside
 *   are then gated separately by permission via the 'can:' middleware.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'You do not have access to this area.');
        }

        $allowed = empty($roles)
            ? $user->roles()->exists()
            : $user->hasAnyRole($roles);

        if (! $allowed) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}