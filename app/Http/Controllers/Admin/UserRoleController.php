<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignRolesRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function __construct(private readonly UserRoleService $userRoles)
    {
    }

    public function index(): View
    {
        return view('admin.users.index', ['users' => $this->userRoles->list()]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(AssignRolesRequest $request, User $user): RedirectResponse
    {
        $this->userRoles->syncRoles($user, $request->validated('roles') ?? []);

        return redirect()->route('admin.users.index')->with('status', "Roles updated for {$user->name}.");
    }
}