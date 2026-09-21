<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles)
    {
    }

    public function index(): View
    {
        return view('admin.roles.index', ['roles' => $this->roles->list()]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'role' => new Role(),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->validated());

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roles->update($role, $request->validated());

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->roles->delete($role);

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }
}