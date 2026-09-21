<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    public function __construct(private readonly PermissionRepository $permissions)
    {
    }

    public function list(): Collection
    {
        return $this->permissions->all();
    }

    public function find(int $id): Permission
    {
        return $this->permissions->find($id);
    }

    public function create(array $data): Permission
    {
        return $this->permissions->create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        return $this->permissions->update($permission, $data);
    }

    public function delete(Permission $permission): void
    {
        $this->permissions->delete($permission);
    }
}