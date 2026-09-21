<?php

namespace App\Repositories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Collection;

class PipelineStageRepository
{
    public function all(): Collection
    {
        return PipelineStage::withCount('projects')->ordered()->get();
    }

    /** @return Collection<int, PipelineStage> */
    public function ordered(): Collection
    {
        return PipelineStage::ordered()->get();
    }

    public function create(array $data): PipelineStage
    {
        return PipelineStage::create($data);
    }

    public function update(PipelineStage $stage, array $data): PipelineStage
    {
        $stage->update($data);

        return $stage;
    }

    public function delete(PipelineStage $stage): void
    {
        $stage->delete();
    }

    /** Position to append a new stage at — one past the current last. */
    public function nextPosition(): int
    {
        return (int) PipelineStage::max('position') + 1;
    }

    /** Clears the default flag everywhere except the given stage. */
    public function clearDefaultExcept(PipelineStage $stage): void
    {
        PipelineStage::where('is_default', true)
            ->whereKeyNot($stage->getKey())
            ->update(['is_default' => false]);
    }

    public function reorder(array $orderedIds): void
    {
        foreach (array_values($orderedIds) as $position => $id) {
            PipelineStage::whereKey($id)->update(['position' => $position + 1]);
        }
    }
}
