<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View roles', 'slug' => 'roles.view', 'group' => 'roles'],
            ['name' => 'Manage roles', 'slug' => 'roles.manage', 'group' => 'roles'],
            ['name' => 'View permissions', 'slug' => 'permissions.view', 'group' => 'permissions'],
            ['name' => 'Manage permissions', 'slug' => 'permissions.manage', 'group' => 'permissions'],
            ['name' => 'View users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Manage user roles', 'slug' => 'users.manage-roles', 'group' => 'users'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Full access to the admin panel.']
        );

        $admin->permissions()->sync(Permission::pluck('id'));

        // A second role with view-only permissions, so the permission-
        // restricted flow (dashboard-only nav, hidden manage controls) is
        // testable right away without hand-crafting a role in the UI.
        $viewer = Role::updateOrCreate(
            ['slug' => 'viewer'],
            ['name' => 'Viewer', 'description' => 'Read-only access to the admin panel.']
        );

        $viewer->permissions()->sync(
            Permission::whereIn('slug', ['roles.view', 'permissions.view', 'users.view'])->pluck('id')
        );
    }
}