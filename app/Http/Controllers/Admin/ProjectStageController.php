<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MoveProjectStageRequest;
use App\Http\Requests\Admin\UpdateStageEntryRequest;
use App\Models\PipelineStage;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;

/**
 * Moving a project along the pipeline, and the per-stage workspace notes
 * that sit alongside each move.
 */
class ProjectStageController extends Controller
{
    public function __construct(private readonly ProjectService $projects) {}

    public function move(MoveProjectStageRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();
        $stage = PipelineStage::findOrFail($validated['pipeline_stage_id']);

        $this->projects->moveToStage(
            $project,
            $stage,
            $request->user(),
            $validated['note'] ?? null,
            array_key_exists('notify_client', $validated) ? (bool) $validated['notify_client'] : null,
        );

        return redirect()->route('admin.projects.show', $project)
            ->with('status', "Moved to {$stage->name}.");
    }

    public function updateEntry(UpdateStageEntryRequest $request, Project $project, PipelineStage $stage): RedirectResponse
    {
        $this->projects->updateStageEntry($project, $stage, $request->validated());

        return redirect()->route('admin.projects.show', $project)
            ->with('status', "Notes saved for {$stage->name}.");
    }
}
