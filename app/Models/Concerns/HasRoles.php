<?php

namespace App\Models\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * Resolved once per request. Every permission check would otherwise
     * hit the database again just to ask the same question.
     */
    protected ?bool $superAdminCache = null;

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
        // Holding a super admin role short-circuits the permission tables
        // entirely: no permission ever has to be attached to that role.
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('slug', $slug))
            ->exists();
    }

    /** True when any role this user holds carries the is_superadmin flag. */
    public function isSuperAdmin(): bool
    {
        return $this->superAdminCache ??= $this->roles()->superAdmin()->exists();
    }

    /** Clears the memoised flag after roles change on a loaded model. */
    public function forgetSuperAdminCache(): void
    {
        $this->superAdminCache = null;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
