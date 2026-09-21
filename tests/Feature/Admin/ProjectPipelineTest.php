<?php

namespace Tests\Feature\Admin;

use App\Mail\ProjectCompletedMail;
use App\Mail\ProjectMediaAddedMail;
use App\Mail\ProjectStageChangedMail;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectPipelineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $client;

    private PipelineStage $intake;

    private PipelineStage $editing;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Mail::fake();
        Storage::fake(config('media.disk'));

        $this->admin = User::factory()->withRole('admin')->create();
        $this->client = User::factory()->client()->create();

        $this->intake = PipelineStage::factory()->default()->create(['name' => 'Intake']);
        $this->editing = PipelineStage::factory()->create(['name' => 'Editing', 'position' => 2]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'pipeline_stage_id' => $this->intake->id,
        ]);
    }

    public function test_moving_a_project_records_the_move_and_emails_the_client(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.projects.stage.move', $this->project), [
                'pipeline_stage_id' => $this->editing->id,
                'note' => 'Assigned to Sam.',
                'notify_client' => '1',
            ])
            ->assertRedirect(route('admin.projects.show', $this->project));

        $this->project->refresh();

        $this->assertSame($this->editing->id, $this->project->pipeline_stage_id);
        $this->assertNotNull($this->project->stage_changed_at);

        $this->assertDatabaseHas('project_stage_histories', [
            'project_id' => $this->project->id,
            'from_stage_id' => $this->intake->id,
            'to_stage_id' => $this->editing->id,
            'changed_by' => $this->admin->id,
            'note' => 'Assigned to Sam.',
        ]);

        // A workspace row is opened for the new stage, and the old one closed.
        $this->assertDatabaseHas('project_stage_entries', [
            'project_id' => $this->project->id,
            'pipeline_stage_id' => $this->editing->id,
        ]);

        Mail::assertSent(ProjectStageChangedMail::class, fn ($mail) => $mail->hasTo($this->client->email));
    }

    public function test_a_silent_stage_does_not_email_the_client(): void
    {
        $internal = PipelineStage::factory()->silent()->create(['name' => 'Internal review']);

        $this->actingAs($this->admin)->put(route('admin.projects.stage.move', $this->project), [
            'pipeline_stage_id' => $internal->id,
        ]);

        Mail::assertNotSent(ProjectStageChangedMail::class);
        $this->assertDatabaseHas('project_stage_histories', [
            'to_stage_id' => $internal->id,
            'client_notified' => false,
        ]);
    }

    public function test_notification_can_be_suppressed_for_a_single_move(): void
    {
        $this->actingAs($this->admin)->put(route('admin.projects.stage.move', $this->project), [
            'pipeline_stage_id' => $this->editing->id,
            'notify_client' => '0',
        ]);

        Mail::assertNotSent(ProjectStageChangedMail::class);
        $this->assertSame($this->editing->id, $this->project->refresh()->pipeline_stage_id);
    }

    public function test_a_final_stage_completes_the_project_and_sends_the_delivery_email(): void
    {
        $delivered = PipelineStage::factory()->final()->create(['name' => 'Delivered']);

        $this->actingAs($this->admin)->put(route('admin.projects.stage.move', $this->project), [
            'pipeline_stage_id' => $delivered->id,
            'notify_client' => '1',
        ]);

        $this->project->refresh();

        $this->assertSame(Project::STATUS_COMPLETED, $this->project->status);
        $this->assertNotNull($this->project->completed_at);

        Mail::assertSent(ProjectCompletedMail::class, fn ($mail) => $mail->hasTo($this->client->email));
        // The delivery email replaces the ordinary stage update.
        Mail::assertNotSent(ProjectStageChangedMail::class);
    }

    public function test_moving_back_out_of_a_final_stage_reopens_the_project(): void
    {
        $delivered = PipelineStage::factory()->final()->create();

        $this->actingAs($this->admin)->put(route('admin.projects.stage.move', $this->project), [
            'pipeline_stage_id' => $delivered->id,
        ]);
        $this->assertTrue($this->project->refresh()->isCompleted());

        $this->actingAs($this->admin)->put(route('admin.projects.stage.move', $this->project), [
            'pipeline_stage_id' => $this->editing->id,
        ]);

        $this->project->refresh();
        $this->assertSame(Project::STATUS_ACTIVE, $this->project->status);
        $this->assertNull($this->project->completed_at);
    }

    public function test_uploading_a_shared_deliverable_emails_the_client(): void
    {
        $this->actingAs($this->admin)->post(route('admin.projects.media.store', $this->project), [
            'pipeline_stage_id' => $this->editing->id,
            'kind' => ProjectMedia::KIND_DELIVERABLE,
            'description' => 'First cut.',
            'visible_to_client' => '1',
            'files' => [UploadedFile::fake()->create('cut-01.mp4', 500, 'video/mp4')],
        ]);

        $media = $this->project->media()->firstOrFail();

        $this->assertSame(ProjectMedia::SOURCE_TEAM, $media->source);
        $this->assertSame($this->editing->id, $media->pipeline_stage_id);
        $this->assertTrue($media->visible_to_client);
        Storage::disk(config('media.disk'))->assertExists($media->path);

        Mail::assertSent(ProjectMediaAddedMail::class, fn ($mail) => $mail->hasTo($this->client->email));
    }

    public function test_an_internal_file_is_not_announced_and_stays_hidden_from_the_client(): void
    {
        $this->actingAs($this->admin)->post(route('admin.projects.media.store', $this->project), [
            'kind' => ProjectMedia::KIND_REFERENCE,
            'visible_to_client' => '0',
            'files' => [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')],
        ]);

        $media = $this->project->media()->firstOrFail();

        $this->assertFalse($media->visible_to_client);
        Mail::assertNotSent(ProjectMediaAddedMail::class);

        $this->actingAs($this->client)
            ->get(route('portal.projects.show', $this->project))
            ->assertOk()
            ->assertDontSee('notes.pdf');

        $this->actingAs($this->client)->get(route('media.download', $media))->assertForbidden();
    }

    public function test_an_admin_can_create_a_project_on_behalf_of_a_client(): void
    {
        $this->actingAs($this->admin)->post(route('admin.projects.store'), [
            'client_id' => $this->client->id,
            'title' => 'Investor update reel',
            'brief' => 'Assemble from the Q3 footage.',
            'type' => 'video',
            'files' => [UploadedFile::fake()->create('q3.mov', 300, 'video/quicktime')],
        ])->assertRedirect();

        $project = Project::where('title', 'Investor update reel')->firstOrFail();

        $this->assertSame($this->client->id, $project->client_id);
        $this->assertSame($this->admin->id, $project->created_by);
        // Raw material we upload for a client is still the client's source file.
        $this->assertSame(ProjectMedia::SOURCE_CLIENT, $project->media()->value('source'));
    }

    public function test_stage_notes_can_be_saved_against_a_stage(): void
    {
        $this->actingAs($this->admin)->put(
            route('admin.projects.stage.entry', [$this->project, $this->editing]),
            ['notes' => 'Waiting on the intro music.', 'client_summary' => 'Your edit is underway.']
        )->assertRedirect();

        $this->assertDatabaseHas('project_stage_entries', [
            'project_id' => $this->project->id,
            'pipeline_stage_id' => $this->editing->id,
            'notes' => 'Waiting on the intro music.',
            'client_summary' => 'Your edit is underway.',
        ]);
    }

    public function test_a_viewer_cannot_move_a_project(): void
    {
        $viewer = User::factory()->withRole('viewer')->create();

        $this->actingAs($viewer)
            ->put(route('admin.projects.stage.move', $this->project), ['pipeline_stage_id' => $this->editing->id])
            ->assertForbidden();

        $this->assertSame($this->intake->id, $this->project->refresh()->pipeline_stage_id);
    }
}
