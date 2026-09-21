<?php

namespace Tests\Feature;

use App\Mail\AccountActivatedMail;
use App\Mail\AccountSuspendedMail;
use App\Mail\ClientInvitationMail;
use App\Mail\NewProjectAlertMail;
use App\Mail\NewRegistrationAlertMail;
use App\Mail\ProjectCompletedMail;
use App\Mail\ProjectMediaAddedMail;
use App\Mail\ProjectStageChangedMail;
use App\Mail\ProjectSubmittedMail;
use App\Mail\RegistrationReceivedMail;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\ProjectStageHistory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Renders every transactional template for real. Mail::fake() in the other
 * suites asserts that a message was queued but never compiles the Blade,
 * so a typo in an email view would otherwise only surface in production.
 */
class TransactionalEmailTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->client = User::factory()->client()->create([
            'name' => 'Dana Cho',
            'company' => 'Cho Media',
            'about' => 'Weekly founder interviews.',
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'pipeline_stage_id' => PipelineStage::factory()->create(['name' => 'Editing'])->id,
        ]);
    }

    public static function mailableProvider(): array
    {
        return [
            'registration received' => ['registrationReceived', 'application'],
            'new registration alert' => ['newRegistrationAlert', 'New account request'],
            'account activated' => ['accountActivated', 'account is live'],
            'account suspended' => ['accountSuspended', 'paused'],
            'client invitation' => ['clientInvitation', 'Set your password'],
            'project submitted' => ['projectSubmitted', 'Track this project'],
            'new project alert' => ['newProjectAlert', 'Open in admin'],
            'stage changed' => ['stageChanged', 'Editing'],
            'media added' => ['mediaAdded', 'cut-01.mp4'],
            'project completed' => ['projectCompleted', 'ready'],
        ];
    }

    #[DataProvider('mailableProvider')]
    public function test_it_renders(string $factory, string $expected): void
    {
        $rendered = $this->{$factory}()->render();

        $this->assertStringContainsString('MPOWER', $rendered);
        $this->assertStringContainsString($expected, $rendered);
        // An unresolved Blade expression would leave the raw directive behind.
        $this->assertStringNotContainsString('@if', $rendered);
    }

    private function registrationReceived(): RegistrationReceivedMail
    {
        return new RegistrationReceivedMail($this->client);
    }

    private function newRegistrationAlert(): NewRegistrationAlertMail
    {
        return new NewRegistrationAlertMail($this->client);
    }

    private function accountActivated(): AccountActivatedMail
    {
        return new AccountActivatedMail($this->client, 'Welcome aboard.');
    }

    private function accountSuspended(): AccountSuspendedMail
    {
        return new AccountSuspendedMail($this->client, 'Invoice overdue.');
    }

    private function clientInvitation(): ClientInvitationMail
    {
        return new ClientInvitationMail($this->client, 'token-123', 'Your first edit is on us.');
    }

    private function projectSubmitted(): ProjectSubmittedMail
    {
        return new ProjectSubmittedMail($this->project);
    }

    private function newProjectAlert(): NewProjectAlertMail
    {
        return new NewProjectAlertMail($this->project);
    }

    private function stageChanged(): ProjectStageChangedMail
    {
        $history = ProjectStageHistory::create([
            'project_id' => $this->project->id,
            'from_stage_id' => null,
            'to_stage_id' => $this->project->pipeline_stage_id,
            'changed_by' => $this->client->id,
            'note' => 'Assigned to Sam.',
        ]);

        return new ProjectStageChangedMail($this->project, $history->load(['fromStage', 'toStage']));
    }

    private function mediaAdded(): ProjectMediaAddedMail
    {
        ProjectMedia::factory()->deliverable()->create([
            'project_id' => $this->project->id,
            'original_name' => 'cut-01.mp4',
        ]);

        return new ProjectMediaAddedMail($this->project, $this->project->media()->get());
    }

    private function projectCompleted(): ProjectCompletedMail
    {
        ProjectMedia::factory()->deliverable()->create([
            'project_id' => $this->project->id,
            'original_name' => 'final-cut.mp4',
        ]);

        return new ProjectCompletedMail($this->project);
    }
}
