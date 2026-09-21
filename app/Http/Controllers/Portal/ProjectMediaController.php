<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreProjectMediaRequest;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;

/** Extra material a client adds to a project already in production. */
class ProjectMediaController extends Controller
{
    public function __construct(private readonly ProjectService $projects)
    {
    }

    public function store(StoreProjectMediaRequest $request, Project $project): RedirectResponse
    {
        $media = $this->projects->addMedia(
            $project,
            $request->file('files', []),
            [
                'kind' => ProjectMedia::KIND_SOURCE,
                'source' => ProjectMedia::SOURCE_CLIENT,
                'description' => $request->validated()['description'] ?? null,
                'visible_to_client' => true,
            ],
            $request->user(),
        );

        return redirect()->route('portal.projects.show', $project)
            ->with('status', $media->count().' file(s) added to '.$project->reference.'.');
    }

    public function destroy(Project $project, ProjectMedia $medium): RedirectResponse
    {
        abort_unless($medium->project_id === $project->id, 404);

        $this->authorize('delete', $medium);

        $this->projects->deleteMedia($medium);

        return redirect()->route('portal.projects.show', $project)->with('status', 'File removed.');
    }
}
