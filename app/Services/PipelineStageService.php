<?php

namespace App\Services;

use App\Models\PipelineStage;
use App\Repositories\PipelineStageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Admin management of the pipeline itself. Two rules are enforced here
 * rather than in the form: exactly one stage can be the default entry
 * point, and a stage still holding projects cannot be deleted out from
 * under them.
 */
class PipelineStageService
{
    public function __construct(private readonly PipelineStageRepository $stages) {}

    public function list(): Collection
    {
        return $this->stages->all();
    }

    public function ordered(): Collection
    {
        return $this->stages->ordered();
    }

    public function create(array $data): PipelineStage
    {
        return DB::transaction(function () use ($data) {
            $stage = $this->stages->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'client_message' => $data['client_message'] ?? null,
                'position' => $data['position'] ?? $this->stages->nextPosition(),
                'color' => $data['color'] ?? 'slate',
                'is_default' => (bool) ($data['is_default'] ?? false),
                'is_final' => (bool) ($data['is_final'] ?? false),
                'notifies_client' => (bool) ($data['notifies_client'] ?? true),
            ]);

            if ($stage->is_default) {
                $this->stages->clearDefaultExcept($stage);
            }

            return $stage;
        });
    }

    public function update(PipelineStage $stage, array $data): PipelineStage
    {
        return DB::transaction(function () use ($stage, $data) {
            $this->stages->update($stage, [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'client_message' => $data['client_message'] ?? null,
                'position' => $data['position'] ?? $stage->position,
                'color' => $data['color'] ?? $stage->color,
                'is_default' => (bool) ($data['is_default'] ?? false),
                'is_final' => (bool) ($data['is_final'] ?? false),
                'notifies_client' => (bool) ($data['notifies_client'] ?? false),
            ]);

            if ($stage->is_default) {
                $this->stages->clearDefaultExcept($stage);
            }

            return $stage->refresh();
        });
    }

    /**
     * @throws ValidationException when projects still sit in the stage
     */
    public function delete(PipelineStage $stage): void
    {
        $inUse = $stage->projects()->count();

        if ($inUse > 0) {
            throw ValidationException::withMessages([
                'stage' => "This stage still holds {$inUse} project(s). Move them to another stage first.",
            ]);
        }

        $this->stages->delete($stage);
    }

    /** @param  array<int, int>  $orderedIds */
    public function reorder(array $orderedIds): void
    {
        DB::transaction(fn () => $this->stages->reorder($orderedIds));
    }
}
