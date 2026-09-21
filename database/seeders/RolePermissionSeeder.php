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

            ['name' => 'View pipeline', 'slug' => 'pipeline.view', 'group' => 'pipeline'],
            ['name' => 'Manage pipeline', 'slug' => 'pipeline.manage', 'group' => 'pipeline'],
            ['name' => 'View clients', 'slug' => 'clients.view', 'group' => 'clients'],
            ['name' => 'Manage clients', 'slug' => 'clients.manage', 'group' => 'clients'],
            ['name' => 'View projects', 'slug' => 'projects.view', 'group' => 'projects'],
            ['name' => 'Manage projects', 'slug' => 'projects.manage', 'group' => 'projects'],
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
            Permission::whereIn('slug', [
                'roles.view', 'permissions.view', 'users.view',
                'pipeline.view', 'clients.view', 'projects.view',
            ])->pluck('id')
        );

        // The production team: everything needed to move work through the
        // pipeline and talk to clients, but not to re-wire the app itself.
        $producer = Role::updateOrCreate(
            ['slug' => 'producer'],
            ['name' => 'Producer', 'description' => 'Runs client projects through the production pipeline.']
        );

        $producer->permissions()->sync(
            Permission::whereIn('slug', [
                'pipeline.view', 'clients.view', 'clients.manage',
                'projects.view', 'projects.manage', 'users.view',
            ])->pluck('id')
        );

        // Clients hold no admin permissions at all — the role exists to
        // open the /portal side of the app, and is attached the moment an
        // account is activated (ClientService::activate()).
        Role::updateOrCreate(
            ['slug' => 'client'],
            ['name' => 'Client', 'description' => 'Uploads projects and follows them through production.']
        );
    }
}
