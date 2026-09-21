<?php

namespace App\Services;

use App\Mail\NewProjectAlertMail;
use App\Mail\ProjectCompletedMail;
use App\Mail\ProjectMediaAddedMail;
use App\Mail\ProjectStageChangedMail;
use App\Mail\ProjectSubmittedMail;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\ProjectStageEntry;
use App\Models\User;
use App\Repositories\ProjectMediaRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Everything that happens to a project after it exists: intake, files, and
 * movement through the pipeline.
 *
 * Database work runs inside a transaction; email is sent after it commits,
 * so a client is never told about a stage change that got rolled back.
 */
class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly ProjectMediaRepository $media,
        private readonly MediaStorageService $storage,
        private readonly MailService $mail,
    ) {}

    /**
     * Intake. Used by both the client portal and the admin "upload on
     * behalf of a client" screen — $actor is who clicked upload, $client
     * is whose project it is.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public function create(User $client, array $data, array $files, ?User $actor = null): Project
    {
        $stage = PipelineStage::default();

        $project = DB::transaction(function () use ($client, $data, $files, $actor, $stage) {
            $project = $this->projects->create([
                'client_id' => $client->id,
                'created_by' => $actor?->id,
                'pipeline_stage_id' => $stage?->id,
                'title' => $data['title'],
                'brief' => $data['brief'] ?? null,
                'type' => $data['type'] ?? 'video',
                'status' => Project::STATUS_ACTIVE,
                'due_date' => $data['due_date'] ?? null,
                'stage_changed_at' => now(),
            ]);

            foreach ($files as $file) {
                $this->storeFile($project, $file, [
                    'uploaded_by' => $actor?->id ?? $client->id,
                    // Raw material for us to work on is a client-source
                    // file even when an admin uploaded it on their behalf.
                    'source' => ProjectMedia::SOURCE_CLIENT,
                    'kind' => ProjectMedia::KIND_SOURCE,
                    'visible_to_client' => true,
                ]);
            }

            if ($stage) {
                $this->projects->firstOrCreateStageEntry($project, $stage->id);

                $this->projects->recordHistory([
                    'project_id' => $project->id,
                    'from_stage_id' => null,
                    'to_stage_id' => $stage->id,
                    'changed_by' => $actor?->id ?? $client->id,
                    'note' => 'Project submitted.',
                    'client_notified' => true,
                ]);
            }

            return $project;
        });

        $project->load(['client', 'stage']);

        $this->mail->send($client, new ProjectSubmittedMail($project));
        $this->mail->sendToStaff(new NewProjectAlertMail($project));

        return $project;
    }

    /**
     * Files added to an existing project — our cuts and revisions against
     * a stage, or extra material from the client. Client-visible team
     * uploads trigger one summary email rather than one per file.
     *
     * @param  array<int, UploadedFile>  $files
     * @return Collection<int, ProjectMedia>
     */
    public function addMedia(Project $project, array $files, array $attributes, User $uploader): Collection
    {
        $stored = DB::transaction(function () use ($project, $files, $attributes, $uploader) {
            $stored = new Collection;

            foreach ($files as $file) {
                $stored->push($this->storeFile($project, $file, [
                    'pipeline_stage_id' => $attributes['pipeline_stage_id'] ?? null,
                    'uploaded_by' => $uploader->id,
                    'source' => $attributes['source'] ?? ProjectMedia::SOURCE_TEAM,
                    'kind' => $attributes['kind'] ?? ProjectMedia::KIND_DELIVERABLE,
                    'description' => $attributes['description'] ?? null,
                    'visible_to_client' => (bool) ($attributes['visible_to_client'] ?? true),
                ]));
            }

            return $stored;
        });

        $notifiable = $stored
            ->where('source', ProjectMedia::SOURCE_TEAM)
            ->where('visible_to_client', true);

        if ($notifiable->isNotEmpty()) {
            $this->mail->send(
                $project->client,
                new ProjectMediaAddedMail($project->load('client'), $notifiable->values()),
            );
        }

        return $stored;
    }

    /**
     * Move a project to another stage. Records the move, opens the stage's
     * workspace row, and emails the client when the target stage is one
     * they should hear about. Landing on a stage flagged is_final also
     * closes the project and sends the delivery email.
     */
    public function moveToStage(Project $project, PipelineStage $stage, User $actor, ?string $note = null, ?bool $notifyClient = null): Project
    {
        $from = $project->pipeline_stage_id;

        if ($from === $stage->id && $project->stage_changed_at !== null) {
            return $project;
        }

        $shouldNotify = $notifyClient ?? $stage->notifies_client;

        $history = DB::transaction(function () use ($project, $stage, $actor, $note, $from, $shouldNotify) {
            $attributes = [
                'pipeline_stage_id' => $stage->id,
                'stage_changed_at' => now(),
            ];

            if ($stage->is_final) {
                $attributes['status'] = Project::STATUS_COMPLETED;
                $attributes['completed_at'] = now();
            } elseif ($project->isCompleted()) {
                // Moved back out of a delivered stage — the project is
                // open again, so don't leave it reading as completed.
                $attributes['status'] = Project::STATUS_ACTIVE;
                $attributes['completed_at'] = null;
            }

            $this->projects->update($project, $attributes);

            // Close out the stage we just left, open the one we entered.
            if ($from) {
                ProjectStageEntry::where('project_id', $project->id)
                    ->where('pipeline_stage_id', $from)
                    ->whereNull('completed_at')
                    ->update(['completed_at' => now()]);
            }

            $this->projects->firstOrCreateStageEntry($project, $stage->id);

            return $this->projects->recordHistory([
                'project_id' => $project->id,
                'from_stage_id' => $from,
                'to_stage_id' => $stage->id,
                'changed_by' => $actor->id,
                'note' => $note,
                'client_notified' => $shouldNotify,
            ]);
        });

        $project->refresh()->load(['client', 'stage']);

        if ($shouldNotify) {
            // A delivery is its own email — the client doesn't need both
            // "moved to Delivered" and "your files are ready".
            $this->mail->send($project->client, $stage->is_final
                ? new ProjectCompletedMail($project)
                : new ProjectStageChangedMail($project, $history->load(['fromStage', 'toStage'])));
        }

        return $project;
    }

    public function updateDetails(Project $project, array $data): Project
    {
        return $this->projects->update($project, collect($data)
            ->only(['title', 'brief', 'type', 'status', 'due_date'])
            ->all());
    }

    /** Notes and assignment for one stage of one project. */
    public function updateStageEntry(Project $project, PipelineStage $stage, array $data): ProjectStageEntry
    {
        $entry = $this->projects->firstOrCreateStageEntry($project, $stage->id);

        $entry->update(collect($data)
            ->only(['notes', 'client_summary', 'assigned_to'])
            ->all());

        return $entry;
    }

    public function deleteMedia(ProjectMedia $media): void
    {
        // Row first: an orphaned object costs storage, a row pointing at a
        // deleted object breaks every screen that lists it.
        $disk = $media->disk;
        $path = $media->path;

        $this->media->delete($media);
        $this->storage->delete($disk, $path);
    }

    public function delete(Project $project): void
    {
        foreach ($project->media as $media) {
            $this->storage->delete($media->disk, $media->path);
        }

        $this->projects->delete($project);
    }

    private function storeFile(Project $project, UploadedFile $file, array $attributes): ProjectMedia
    {
        $stored = $this->storage->store($file, $project, $attributes['pipeline_stage_id'] ?? null);

        return $this->media->create(array_merge($attributes, $stored, [
            'project_id' => $project->id,
        ]));
    }
}
