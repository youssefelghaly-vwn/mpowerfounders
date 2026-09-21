<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(PipelineStageSeeder::class);
    }

    /** A role with the flag and deliberately no permissions attached. */
    private function superAdmin(): User
    {
        $role = Role::create([
            'name' => 'Owner',
            'is_superadmin' => true,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user->fresh();
    }

    public function test_a_super_admin_role_needs_no_permissions_to_reach_every_admin_section(): void
    {
        $user = $this->superAdmin();

        $this->assertCount(0, $user->roles()->first()->permissions);

        foreach ([
            'admin.dashboard',
            'admin.roles.index',
            'admin.roles.create',
            'admin.permissions.index',
            'admin.permissions.create',
            'admin.users.index',
            'admin.pipeline.index',
            'admin.pipeline.create',
            'admin.clients.index',
            'admin.clients.create',
            'admin.projects.index',
            'admin.projects.create',
        ] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_a_super_admin_passes_permission_checks_and_policies(): void
    {
        $user = $this->superAdmin();

        $this->assertTrue($user->isSuperAdmin());
        $this->assertTrue($user->hasPermission('anything.at.all'));
        $this->assertTrue($user->can('projects.manage'));

        $project = Project::factory()->create();
        $this->assertTrue($user->can('update', $project));

        $stage = PipelineStage::where('slug', 'editing')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.projects.stage.move', $project), ['pipeline_stage_id' => $stage->id])
            ->assertRedirect();

        $this->assertSame($stage->id, $project->refresh()->pipeline_stage_id);
    }

    public function test_an_ordinary_role_still_needs_its_permissions(): void
    {
        $viewer = User::factory()->withRole('viewer')->create();

        $this->assertFalse($viewer->isSuperAdmin());
        $this->actingAs($viewer)->get(route('admin.roles.create'))->assertForbidden();
    }

    public function test_only_a_super_admin_can_grant_the_flag(): void
    {
        // Holds roles.manage but not the flag — the field must be ignored,
        // otherwise this is a one-request escalation to full access.
        $manager = User::factory()->withRole('producer')->create();
        $manager->roles()->first()->permissions()->sync(
            Permission::whereIn('slug', ['roles.view', 'roles.manage'])->pluck('id')
        );

        $this->actingAs($manager)->post(route('admin.roles.store'), [
            'name' => 'Backdoor',
            'is_superadmin' => '1',
        ])->assertRedirect();

        $this->assertFalse(Role::where('name', 'Backdoor')->firstOrFail()->is_superadmin);
    }

    public function test_a_super_admin_can_grant_and_revoke_the_flag(): void
    {
        $user = $this->superAdmin();
        $target = Role::create(['name' => 'Studio lead']);

        $this->actingAs($user)->put(route('admin.roles.update', $target), [
            'name' => 'Studio lead',
            'is_superadmin' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertTrue($target->refresh()->is_superadmin);

        $this->actingAs($user)->put(route('admin.roles.update', $target), [
            'name' => 'Studio lead',
            'is_superadmin' => '0',
        ])->assertSessionHasNoErrors();

        $this->assertFalse($target->refresh()->is_superadmin);
    }

    public function test_the_last_super_admin_role_cannot_be_demoted_or_deleted(): void
    {
        $user = $this->superAdmin();
        $owner = $user->roles()->first();

        // The seeded 'admin' role also carries the flag, so clear it first
        // to make 'Owner' genuinely the last one.
        Role::where('slug', 'admin')->update(['is_superadmin' => false]);

        $this->actingAs($user)->put(route('admin.roles.update', $owner), [
            'name' => 'Owner',
            'is_superadmin' => '0',
        ])->assertSessionHasErrors('is_superadmin');

        $this->assertTrue($owner->refresh()->is_superadmin);

        $this->actingAs($user)
            ->delete(route('admin.roles.destroy', $owner))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', ['id' => $owner->id]);
    }

    public function test_a_super_admin_is_still_not_a_client(): void
    {
        // The flag grants abilities, not role membership — the portal is
        // gated on holding the 'client' role, and stays shut.
        $this->actingAs($this->superAdmin())->get(route('portal.dashboard'))->assertForbidden();
    }
}
