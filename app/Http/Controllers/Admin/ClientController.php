<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Http\Requests\Admin\UpdateClientStatusRequest;
use App\Models\User;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Client accounts: the pending queue, activation, and adding a client
 * ourselves (which sends them a password-set link rather than making up a
 * password on their behalf).
 */
class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clients,
        private readonly ClientRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.clients.index', [
            'clients' => $this->repository->paginate($request->only('status', 'role', 'search')),
            'filters' => $request->only('status', 'role', 'search'),
            'pendingCount' => $this->repository->pendingCount(),
            'statuses' => User::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.clients.create', ['client' => new User]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = $this->clients->createByAdmin($request->validated(), $request->user());

        return redirect()->route('admin.clients.show', $client)
            ->with('status', "{$client->name} added — a welcome email with a password link is on its way.");
    }

    public function show(User $client): View
    {
        return view('admin.clients.show', [
            'client' => $client->load('roles'),
            'projects' => $client->projects()->with('stage')->latest('id')->get(),
        ]);
    }

    public function edit(User $client): View
    {
        return view('admin.clients.edit', ['client' => $client]);
    }

    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        $this->clients->update($client, $request->validated());

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client details updated.');
    }

    /**
     * Activate or suspend. Activation is what attaches the 'client' role
     * and sends the welcome email — see ClientService::activate().
     */
    public function updateStatus(UpdateClientStatusRequest $request, User $client): RedirectResponse
    {
        $validated = $request->validated();
        $notify = (bool) ($validated['notify'] ?? true);

        if ($validated['status'] === User::STATUS_ACTIVE) {
            $this->clients->activate($client, $request->user(), $validated['note'] ?? null, $notify);
            $message = "{$client->name}'s account is now active".($notify ? ' and they have been emailed.' : '.');
        } else {
            $this->clients->suspend($client, $validated['note'] ?? null, $notify);
            $message = "{$client->name}'s account has been paused.";
        }

        return redirect()->route('admin.clients.show', $client)->with('status', $message);
    }

    public function resendInvitation(Request $request, User $client): RedirectResponse
    {
        $validated = $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        $this->clients->resendInvitation($client, $validated['note'] ?? null);

        return redirect()->route('admin.clients.show', $client)
            ->with('status', "A fresh password link has been emailed to {$client->email}.");
    }
}
