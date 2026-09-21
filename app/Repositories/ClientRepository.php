<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Reads and writes over the users table from the "client account" angle —
 * separate from UserRepository, which exists to serve the role-assignment
 * screens.
 */
class ClientRepository
{
    /**
     * Everyone who signed up or was added by us, newest first. Pending
     * accounts float to the top: they are the ones waiting on a decision.
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->withCount('projects')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(($filters['role'] ?? null) === 'client', fn ($query) => $query->clients())
            ->when($filters['search'] ?? null, function ($query, $search) {
                $term = '%'.$search.'%';

                $query->where(fn ($inner) => $inner
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('company', 'like', $term));
            })
            ->orderByRaw("case when status = '".User::STATUS_PENDING."' then 0 else 1 end")
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function pendingCount(): int
    {
        return User::pending()->count();
    }

    public function activeClientCount(): int
    {
        return User::clients()->where('status', User::STATUS_ACTIVE)->count();
    }

    /** Recipients for internal alerts: every non-client role holder. */
    public function staff(): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($roles) => $roles->where('slug', '!=', 'client'))
            ->where('status', '!=', User::STATUS_SUSPENDED)
            ->get();
    }
}
