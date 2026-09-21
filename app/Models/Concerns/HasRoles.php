<?php

namespace App\Models\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function hasPermission(string $slug): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($query) => $query->where('slug', $slug))
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
