<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function allWithRoles(): Collection
    {
        return User::with('roles')->orderBy('name')->get();
    }

    public function find(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    public function syncRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }
}