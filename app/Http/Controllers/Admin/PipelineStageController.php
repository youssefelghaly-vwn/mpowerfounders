<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePipelineStageRequest;
use App\Http\Requests\Admin\UpdatePipelineStageRequest;
use App\Models\PipelineStage;
use App\Repositories\ProjectRepository;
use App\Services\PipelineStageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Pipeline management — the admin-defined stages every project travels
 * through (Intake, Preparing, Editing, Review, Delivered, ...).
 */
class PipelineStageController extends Controller
{
    public function __construct(
        private readonly PipelineStageService $stages,
        private readonly ProjectRepository $projects,
    ) {}

    public function index(): View
    {
        return view('admin.pipeline.index', [
            'stages' => $this->stages->list(),
            'countsByStage' => $this->projects->countsByStage(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pipeline.create', ['stage' => new PipelineStage(['color' => 'slate', 'notifies_client' => true])]);
    }

    public function store(StorePipelineStageRequest $request): RedirectResponse
    {
        $stage = $this->stages->create($request->validated());

        return redirect()->route('admin.pipeline.index')
            ->with('status', "Stage \"{$stage->name}\" created.");
    }

    public function edit(PipelineStage $stage): View
    {
        return view('admin.pipeline.edit', ['stage' => $stage]);
    }

    public function update(UpdatePipelineStageRequest $request, PipelineStage $stage): RedirectResponse
    {
        $this->stages->update($stage, $request->validated());

        return redirect()->route('admin.pipeline.index')
            ->with('status', "Stage \"{$stage->name}\" updated.");
    }

    public function destroy(PipelineStage $stage): RedirectResponse
    {
        try {
            $this->stages->delete($stage);
        } catch (ValidationException $exception) {
            return redirect()->route('admin.pipeline.index')
                ->withErrors($exception->errors());
        }

        return redirect()->route('admin.pipeline.index')->with('status', 'Stage deleted.');
    }

    /** Drag-to-reorder from the pipeline board. */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stages' => ['required', 'array'],
            'stages.*' => ['integer', 'exists:pipeline_stages,id'],
        ]);

        $this->stages->reorder($validated['stages']);

        return redirect()->route('admin.pipeline.index')->with('status', 'Pipeline order updated.');
    }
}
