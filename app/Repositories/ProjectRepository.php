<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectStageEntry;
use App\Models\ProjectStageHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    /**
     * Admin listing. Filters are all optional and come straight from the
     * query string: stage, status, client, and a free-text search over the
     * reference/title and the client's name or email.
     */
    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Project::query()
            ->with(['client', 'stage'])
            ->withCount('media')
            ->when($filters['stage'] ?? null, fn ($query, $stage) => $query->where('pipeline_stage_id', $stage))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['client'] ?? null, fn ($query, $client) => $query->where('client_id', $client))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $term = '%'.$search.'%';

                $query->where(function ($inner) use ($term) {
                    $inner->where('reference', 'like', $term)
                        ->orWhere('title', 'like', $term)
                        ->orWhereHas('client', fn ($client) => $client
                            ->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term));
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForClient(User $client, int $perPage = 10): LengthAwarePaginator
    {
        return Project::forClient($client)
            ->with('stage')
            ->withCount(['media' => fn ($query) => $query->visibleToClient()])
            ->latest('id')
            ->paginate($perPage);
    }

    /** @return Collection<int, Project> */
    public function recent(int $limit = 5): Collection
    {
        return Project::with(['client', 'stage'])->latest('id')->take($limit)->get();
    }

    /** Project counts keyed by pipeline stage id — powers the admin board. */
    public function countsByStage(): array
    {
        return Project::query()
            ->whereNotNull('pipeline_stage_id')
            ->where('status', '!=', Project::STATUS_CANCELLED)
            ->selectRaw('pipeline_stage_id, count(*) as aggregate')
            ->groupBy('pipeline_stage_id')
            ->pluck('aggregate', 'pipeline_stage_id')
            ->all();
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    /**
     * The workspace row for a project/stage pair, created on first entry.
     */
    public function firstOrCreateStageEntry(Project $project, int $stageId, array $attributes = []): ProjectStageEntry
    {
        return ProjectStageEntry::firstOrCreate(
            ['project_id' => $project->id, 'pipeline_stage_id' => $stageId],
            array_merge(['entered_at' => now()], $attributes),
        );
    }

    public function recordHistory(array $data): ProjectStageHistory
    {
        return ProjectStageHistory::create($data);
    }
}
