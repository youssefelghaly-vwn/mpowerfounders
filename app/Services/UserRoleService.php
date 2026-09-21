<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserRoleService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function list(): Collection
    {
        return $this->users->allWithRoles();
    }

    public function find(int $id): User
    {
        return $this->users->find($id);
    }

    public function syncRoles(User $user, array $roleIds): User
    {
        $this->users->syncRoles($user, $roleIds);

        return $user;
    }
}