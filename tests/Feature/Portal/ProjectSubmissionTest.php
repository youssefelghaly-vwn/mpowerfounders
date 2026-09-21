<?php

namespace Tests\Feature\Portal;

use App\Mail\NewProjectAlertMail;
use App\Mail\ProjectSubmittedMail;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(PipelineStageSeeder::class);

        Mail::fake();
        Storage::fake(config('media.disk'));

        $this->client = User::factory()->client()->create();
    }

    public function test_a_client_can_upload_a_video_with_a_brief(): void
    {
        $file = UploadedFile::fake()->create('episode-14.mp4', 2048, 'video/mp4');

        $response = $this->actingAs($this->client)->post(route('portal.projects.store'), [
            'title' => 'Episode 14',
            'brief' => 'Cut to eight minutes and pull three clips.',
            'type' => 'podcast',
            'files' => [$file],
        ]);

        $project = Project::firstOrFail();
        $response->assertRedirect(route('portal.projects.show', $project));

        $this->assertSame($this->client->id, $project->client_id);
        $this->assertSame('Cut to eight minutes and pull three clips.', $project->brief);
        $this->assertMatchesRegularExpression('/^MP-[A-Z0-9]{6}$/', $project->reference);

        // Lands in the stage flagged as the pipeline's entry point.
        $this->assertSame(PipelineStage::where('is_default', true)->value('id'), $project->pipeline_stage_id);

        $media = $project->media()->firstOrFail();
        $this->assertSame('episode-14.mp4', $media->original_name);
        $this->assertSame(ProjectMedia::SOURCE_CLIENT, $media->source);
        Storage::disk(config('media.disk'))->assertExists($media->path);

        // The first stage is recorded, so the client timeline is never empty.
        $this->assertDatabaseCount('project_stage_histories', 1);
        $this->assertDatabaseCount('project_stage_entries', 1);
    }

    public function test_submitting_emails_the_client_and_the_team(): void
    {
        $staff = User::factory()->withRole('producer')->create();

        $this->actingAs($this->client)->post(route('portal.projects.store'), [
            'title' => 'Episode 14',
            'brief' => 'Cut it down.',
            'type' => 'video',
            'files' => [UploadedFile::fake()->create('raw.mp4', 100, 'video/mp4')],
        ]);

        Mail::assertSent(ProjectSubmittedMail::class, fn ($mail) => $mail->hasTo($this->client->email));
        Mail::assertSent(NewProjectAlertMail::class, fn ($mail) => $mail->hasTo($staff->email));
    }

    public function test_a_brief_and_at_least_one_file_are_required(): void
    {
        $this->actingAs($this->client)
            ->post(route('portal.projects.store'), ['title' => 'Episode 14', 'type' => 'video'])
            ->assertSessionHasErrors(['brief', 'files']);

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_an_unsupported_file_type_is_rejected(): void
    {
        $this->actingAs($this->client)
            ->post(route('portal.projects.store'), [
                'title' => 'Episode 14',
                'brief' => 'Cut it down.',
                'type' => 'video',
                'files' => [UploadedFile::fake()->create('payload.php', 10, 'application/x-php')],
            ])
            ->assertSessionHasErrors('files.0');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_a_client_cannot_open_another_clients_project(): void
    {
        $theirs = Project::factory()->create();

        $this->actingAs($this->client)
            ->get(route('portal.projects.show', $theirs))
            ->assertForbidden();
    }

    public function test_a_pending_account_cannot_reach_the_portal(): void
    {
        $pending = User::factory()->pending()->create();

        $this->actingAs($pending)->get(route('portal.dashboard'))->assertRedirect(route('login'));
    }
}
