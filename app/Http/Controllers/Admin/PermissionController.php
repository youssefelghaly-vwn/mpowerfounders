<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissions)
    {
    }

    public function index(): View
    {
        return view('admin.permissions.index', ['permissions' => $this->permissions->list()]);
    }

    public function create(): View
    {
        return view('admin.permissions.create', ['permission' => new Permission()]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissions->create($request->validated());

        return redirect()->route('admin.permissions.index')->with('status', 'Permission created.');
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.edit', ['permission' => $permission]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissions->update($permission, $request->validated());

        return redirect()->route('admin.permissions.index')->with('status', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissions->delete($permission);

        return redirect()->route('admin.permissions.index')->with('status', 'Permission deleted.');
    }
}