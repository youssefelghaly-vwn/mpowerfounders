<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Project $project;

    private ProjectMedia $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Storage::fake(config('media.disk'));

        $this->client = User::factory()->client()->create();
        $this->project = Project::factory()->create(['client_id' => $this->client->id]);
        $this->media = ProjectMedia::factory()->create([
            'project_id' => $this->project->id,
            'uploaded_by' => $this->client->id,
            'disk' => config('media.disk'),
        ]);

        Storage::disk(config('media.disk'))->put($this->media->path, 'bytes');
    }

    public function test_a_client_can_download_their_own_file(): void
    {
        // Bytes are never proxied through PHP: an authorised request is
        // answered with a redirect to a short-lived signed storage URL.
        $response = $this->actingAs($this->client)->get(route('media.download', $this->media));

        $response->assertRedirect();
        $this->assertStringContainsString(basename($this->media->path), $response->headers->get('Location'));
    }

    public function test_another_client_cannot(): void
    {
        $stranger = User::factory()->client()->create();

        $this->actingAs($stranger)->get(route('media.download', $this->media))->assertForbidden();
    }

    public function test_staff_can_open_any_file(): void
    {
        $producer = User::factory()->withRole('producer')->create();

        $this->actingAs($producer)->get(route('media.show', $this->media))->assertRedirect();
    }

    public function test_a_guest_is_sent_to_the_sign_in_page(): void
    {
        $this->get(route('media.download', $this->media))->assertRedirect(route('login'));
    }

    public function test_a_client_can_remove_their_own_upload_but_not_ours(): void
    {
        $ours = ProjectMedia::factory()->deliverable()->create([
            'project_id' => $this->project->id,
            'disk' => config('media.disk'),
        ]);

        $this->actingAs($this->client)
            ->delete(route('portal.projects.media.destroy', [$this->project, $ours]))
            ->assertForbidden();

        $this->actingAs($this->client)
            ->delete(route('portal.projects.media.destroy', [$this->project, $this->media]))
            ->assertRedirect(route('portal.projects.show', $this->project));

        $this->assertDatabaseMissing('project_media', ['id' => $this->media->id]);
        Storage::disk(config('media.disk'))->assertMissing($this->media->path);
    }

    public function test_a_file_cannot_be_reached_through_a_project_it_does_not_belong_to(): void
    {
        $other = Project::factory()->create(['client_id' => $this->client->id]);

        $this->actingAs($this->client)
            ->delete(route('portal.projects.media.destroy', [$other, $this->media]))
            ->assertNotFound();
    }
}
