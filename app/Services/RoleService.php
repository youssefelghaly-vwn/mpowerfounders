<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function __construct(private readonly RoleRepository $roles) {}

    public function list(): Collection
    {
        return $this->roles->all();
    }

    public function find(int $id): Role
    {
        return $this->roles->find($id);
    }

    public function create(array $data, ?User $actor = null): Role
    {
        $role = $this->roles->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_superadmin' => $this->resolveSuperAdminFlag(false, $data, $actor),
        ]);

        if (! empty($data['permissions'])) {
            $this->roles->syncPermissions($role, $data['permissions']);
        }

        return $role;
    }

    public function update(Role $role, array $data, ?User $actor = null): Role
    {
        $isSuperAdmin = $this->resolveSuperAdminFlag($role->is_superadmin, $data, $actor);

        if ($role->is_superadmin && ! $isSuperAdmin) {
            $this->guardLastSuperAdminRole($role, 'is_superadmin');
        }

        $this->roles->update($role, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_superadmin' => $isSuperAdmin,
        ]);

        $this->roles->syncPermissions($role, $data['permissions'] ?? []);

        return $role;
    }

    public function delete(Role $role): void
    {
        if ($role->is_superadmin) {
            $this->guardLastSuperAdminRole($role, 'role');
        }

        $this->roles->delete($role);
    }

    /**
     * Who may hand out the super admin flag: only someone who already
     * holds it. Without this, anyone with 'roles.manage' could promote
     * themselves past every permission check in the app — the flag is a
     * bigger grant than any permission it could otherwise assign itself.
     *
     * A non-super-admin editing a role simply keeps the current value
     * rather than being shown an error for a control they never saw.
     */
    private function resolveSuperAdminFlag(bool $current, array $data, ?User $actor): bool
    {
        if (! $actor?->isSuperAdmin()) {
            return $current;
        }

        return (bool) ($data['is_superadmin'] ?? false);
    }

    /**
     * @throws ValidationException when this is the only super admin role left
     */
    private function guardLastSuperAdminRole(Role $role, string $key): void
    {
        if ($this->roles->otherSuperAdminCount($role) > 0) {
            return;
        }

        throw ValidationException::withMessages([
            $key => "\"{$role->name}\" is the only super admin role. Flag another role as super admin first, or you will lock everyone out of the admin panel.",
        ]);
    }
}
