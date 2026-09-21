<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    public function all(): Collection
    {
        return Role::withCount(['users', 'permissions'])->orderBy('name')->get();
    }

    public function find(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        return $role;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }

    /** How many other roles still carry the super admin flag. */
    public function otherSuperAdminCount(Role $role): int
    {
        return Role::superAdmin()->whereKeyNot($role->getKey())->count();
    }
}
