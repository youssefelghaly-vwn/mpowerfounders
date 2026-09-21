<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(private readonly RoleRepository $roles)
    {
    }

    public function list(): Collection
    {
        return $this->roles->all();
    }

    public function find(int $id): Role
    {
        return $this->roles->find($id);
    }

    public function create(array $data): Role
    {
        $role = $this->roles->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (! empty($data['permissions'])) {
            $this->roles->syncPermissions($role, $data['permissions']);
        }

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $this->roles->update($role, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $this->roles->syncPermissions($role, $data['permissions'] ?? []);

        return $role;
    }

    public function delete(Role $role): void
    {
        $this->roles->delete($role);
    }
}