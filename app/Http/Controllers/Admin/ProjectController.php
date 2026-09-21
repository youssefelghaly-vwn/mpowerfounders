<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projects,
        private readonly ProjectRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.projects.index', [
            'projects' => $this->repository->paginateForAdmin($request->only('stage', 'status', 'client', 'search')),
            'filters' => $request->only('stage', 'status', 'client', 'search'),
            'stages' => PipelineStage::ordered()->get(),
            'countsByStage' => $this->repository->countsByStage(),
            'statuses' => Project::STATUSES,
        ]);
    }

    /** Upload on a client's behalf — same intake path as the portal. */
    public function create(Request $request): View
    {
        return view('admin.projects.create', [
            'project' => new Project(['type' => 'video']),
            'clients' => User::clients()->orderBy('name')->get(),
            'selectedClient' => $request->integer('client') ?: null,
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $client = User::findOrFail($validated['client_id']);

        $project = $this->projects->create(
            $client,
            $validated,
            $request->file('files', []),
            $request->user(),
        );

        return redirect()->route('admin.projects.show', $project)
            ->with('status', "Project {$project->reference} created for {$client->name}.");
    }

    public function show(Project $project): View
    {
        $project->load([
            'client',
            'creator',
            'stage',
            'media' => fn ($query) => $query->with(['uploader', 'stage'])->latest('id'),
            'stageEntries.stage',
            'stageEntries.assignee',
            'histories.fromStage',
            'histories.toStage',
            'histories.author',
        ]);

        return view('admin.projects.show', [
            'project' => $project,
            'stages' => PipelineStage::ordered()->get(),
            'statuses' => Project::STATUSES,
            'staff' => User::query()
                ->whereHas('roles', fn ($roles) => $roles->where('slug', '!=', 'client'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projects->updateDetails($project, $request->validated());

        return redirect()->route('admin.projects.show', $project)->with('status', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $reference = $project->reference;

        $this->projects->delete($project);

        return redirect()->route('admin.projects.index')
            ->with('status', "Project {$reference} and its files were deleted.");
    }
}
