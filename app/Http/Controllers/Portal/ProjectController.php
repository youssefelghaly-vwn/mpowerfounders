<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreProjectRequest;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The client's own view of their work: submit material with a description
 * of the request, then follow it through the pipeline.
 */
class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projects,
        private readonly ProjectRepository $repository,
    ) {
    }

    public function index(Request $request): View
    {
        return view('portal.projects.index', [
            'projects' => $this->repository->paginateForClient($request->user()),
        ]);
    }

    public function create(): View
    {
        return view('portal.projects.create', ['project' => new Project(['type' => 'video'])]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projects->create(
            $request->user(),
            $request->validated(),
            $request->file('files', []),
        );

        return redirect()->route('portal.projects.show', $project)
            ->with('status', "Thanks — {$project->reference} is with our team. We'll email you as it moves through production.");
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'stage',
            // Internal working files stay internal.
            'media' => fn ($query) => $query->visibleToClient()->with('stage')->latest('id'),
            'histories' => fn ($query) => $query->where('client_notified', true)->with(['fromStage', 'toStage']),
            'stageEntries.stage',
        ]);

        return view('portal.projects.show', ['project' => $project]);
    }
}
