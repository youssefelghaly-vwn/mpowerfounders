<?php

namespace Tests\Feature;

use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cheap breadth: every screen the two audiences can reach is rendered once
 * with realistic data. Catches Blade mistakes that unit-level tests of the
 * services would never touch.
 */
class ScreenRenderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $client;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(PipelineStageSeeder::class);

        $this->admin = User::factory()->withRole('admin')->create();
        $this->client = User::factory()->client()->create();

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'created_by' => $this->admin->id,
            'pipeline_stage_id' => PipelineStage::where('is_default', true)->value('id'),
        ]);

        ProjectMedia::factory()->create(['project_id' => $this->project->id]);
        ProjectMedia::factory()->deliverable()->create([
            'project_id' => $this->project->id,
            'pipeline_stage_id' => PipelineStage::where('slug', 'editing')->value('id'),
        ]);
        ProjectMedia::factory()->internal()->create(['project_id' => $this->project->id]);
    }

    public function test_guest_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->get(route('register.pending'))->assertOk();
        $this->get(route('password.request'))->assertOk();
    }

    public function test_admin_screens_render(): void
    {
        $stage = PipelineStage::ordered()->first();

        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();

        $this->actingAs($this->admin)->get(route('admin.pipeline.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pipeline.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.pipeline.edit', $stage))->assertOk();

        $this->actingAs($this->admin)->get(route('admin.clients.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.clients.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.clients.show', $this->client))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.clients.edit', $this->client))->assertOk();

        $this->actingAs($this->admin)->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.projects.create'))->assertOk();
        $this->actingAs($this->admin)
            ->get(route('admin.projects.show', $this->project))
            ->assertOk()
            ->assertSee($this->project->reference);
    }

    public function test_portal_screens_render(): void
    {
        $this->actingAs($this->client)->get(route('portal.dashboard'))->assertOk();
        $this->actingAs($this->client)->get(route('portal.projects.index'))->assertOk();
        $this->actingAs($this->client)->get(route('portal.projects.create'))->assertOk();
        $this->actingAs($this->client)
            ->get(route('portal.projects.show', $this->project))
            ->assertOk()
            ->assertSee($this->project->reference);
    }

    public function test_a_viewer_sees_the_read_only_admin(): void
    {
        $viewer = User::factory()->withRole('viewer')->create();

        $this->actingAs($viewer)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.projects.show', $this->project))->assertOk();
        $this->actingAs($viewer)->get(route('admin.projects.create'))->assertForbidden();
    }

    public function test_a_user_with_a_role_but_no_permissions_sees_an_empty_dashboard(): void
    {
        $nobody = User::factory()->withRole('bystander')->create();

        $this->actingAs($nobody)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee("doesn't have access to any admin sections", escape: false);
    }
}
