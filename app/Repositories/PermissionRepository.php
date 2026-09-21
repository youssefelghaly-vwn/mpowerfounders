<?php

namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    public function all(): Collection
    {
        return Permission::withCount('roles')->orderBy('group')->orderBy('name')->get();
    }

    public function find(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission;
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }
}