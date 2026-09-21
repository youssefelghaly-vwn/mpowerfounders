<?php

namespace Tests\Feature\Admin;

use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->withRole('admin')->create();
    }

    public function test_an_admin_can_create_a_stage(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pipeline.store'), [
                'name' => 'Colour grade',
                'description' => 'Grading pass.',
                'client_message' => 'We are grading your footage.',
                'color' => 'violet',
                'notifies_client' => '1',
            ])
            ->assertRedirect(route('admin.pipeline.index'));

        $stage = PipelineStage::where('name', 'Colour grade')->firstOrFail();

        $this->assertSame('colour-grade', $stage->slug);
        $this->assertTrue($stage->notifies_client);
        // Appended to the end of the pipeline rather than colliding at 0.
        $this->assertSame(1, $stage->position);
    }

    public function test_only_one_stage_can_be_the_entry_point(): void
    {
        $first = PipelineStage::factory()->default()->create();

        $this->actingAs($this->admin)->post(route('admin.pipeline.store'), [
            'name' => 'New intake',
            'is_default' => '1',
        ]);

        $this->assertFalse($first->refresh()->is_default);
        $this->assertTrue(PipelineStage::where('name', 'New intake')->firstOrFail()->is_default);
    }

    public function test_a_stage_holding_projects_cannot_be_deleted(): void
    {
        $stage = PipelineStage::factory()->create();
        Project::factory()->create(['pipeline_stage_id' => $stage->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.pipeline.destroy', $stage))
            ->assertSessionHasErrors('stage');

        $this->assertDatabaseHas('pipeline_stages', ['id' => $stage->id]);
    }

    public function test_an_empty_stage_can_be_deleted(): void
    {
        $stage = PipelineStage::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.pipeline.destroy', $stage))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('pipeline_stages', ['id' => $stage->id]);
    }

    public function test_stages_can_be_reordered(): void
    {
        $a = PipelineStage::factory()->create(['position' => 1]);
        $b = PipelineStage::factory()->create(['position' => 2]);

        $this->actingAs($this->admin)->post(route('admin.pipeline.reorder'), [
            'stages' => [$b->id, $a->id],
        ]);

        $this->assertSame(1, $b->refresh()->position);
        $this->assertSame(2, $a->refresh()->position);
    }

    public function test_a_viewer_can_see_the_pipeline_but_not_change_it(): void
    {
        $viewer = User::factory()->withRole('viewer')->create();

        $this->actingAs($viewer)->get(route('admin.pipeline.index'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.pipeline.create'))->assertForbidden();
    }

    public function test_a_client_cannot_reach_pipeline_management(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get(route('admin.pipeline.index'))->assertForbidden();
    }
}
