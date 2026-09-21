<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_superadmin'];

    protected function casts(): array
    {
        return ['is_superadmin' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Role $role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }

    public function hasPermission(string $slug): bool
    {
        // A super admin role answers yes to everything — that is the whole
        // point of the flag, and it means its permission list is decoration.
        if ($this->is_superadmin) {
            return true;
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }

    public function scopeSuperAdmin(Builder $query): Builder
    {
        return $query->where('is_superadmin', true);
    }
}
