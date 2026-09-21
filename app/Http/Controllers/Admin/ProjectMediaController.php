<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectMediaRequest;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;

/** Files our own side attaches to a project, usually against a stage. */
class ProjectMediaController extends Controller
{
    public function __construct(private readonly ProjectService $projects) {}

    public function store(StoreProjectMediaRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        $media = $this->projects->addMedia(
            $project,
            $request->file('files', []),
            [
                'pipeline_stage_id' => $validated['pipeline_stage_id'] ?? null,
                'kind' => $validated['kind'],
                'description' => $validated['description'] ?? null,
                'source' => ProjectMedia::SOURCE_TEAM,
                'visible_to_client' => (bool) ($validated['visible_to_client'] ?? false),
            ],
            $request->user(),
        );

        return redirect()->route('admin.projects.show', $project)
            ->with('status', $media->count().' file(s) uploaded.');
    }

    public function destroy(Project $project, ProjectMedia $medium): RedirectResponse
    {
        abort_unless($medium->project_id === $project->id, 404);

        $this->projects->deleteMedia($medium);

        return redirect()->route('admin.projects.show', $project)->with('status', 'File deleted.');
    }
}
