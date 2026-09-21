<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        // Credentials can be right while the account still isn't usable:
        // pending accounts have not been reviewed yet, suspended ones have
        // had access withdrawn. Say which, rather than "invalid login".
        if (! $user->isActive()) {
            Auth::guard('web')->logout();

            return back()->withInput($request->only('email'))->withErrors([
                'email' => $user->status === User::STATUS_SUSPENDED
                    ? 'Your account has been paused. Please contact us if you think this is a mistake.'
                    : 'Your account is still awaiting activation. We will email you as soon as it is live.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($user->dashboardUrl());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
