<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered as the 'active' middleware alias.
 *
 * Registration creates a pending account, so authentication alone is not
 * enough to get anywhere: a signed-in user whose account has not been
 * activated (or has since been suspended) is logged straight back out with
 * an explanation rather than left wandering a half-usable app.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isActive()) {
            $message = $user->status === User::STATUS_SUSPENDED
                ? 'Your account has been paused. Please contact us if you think this is a mistake.'
                : 'Your account is still awaiting activation. We will email you as soon as it is live.';

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
