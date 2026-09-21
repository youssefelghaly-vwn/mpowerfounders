<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\ClientService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Registering here is applying, not joining: the account is created
 * 'pending' with no role attached, so it can't sign in yet. An admin
 * activates it from Admin -> Clients, which attaches the 'client' role and
 * sends the welcome email (ClientService::activate()).
 *
 * The user is deliberately NOT logged in afterwards — there is nothing for
 * them to do until that happens.
 */
class RegisteredUserController extends Controller
{
    public function __construct(private readonly ClientService $clients) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->clients->register($request->validated());

        event(new Registered($user));

        return redirect()->route('register.pending');
    }

    /** Standalone page so a refresh doesn't lose the confirmation. */
    public function pending(): View
    {
        return view('auth.registration-pending');
    }
}
