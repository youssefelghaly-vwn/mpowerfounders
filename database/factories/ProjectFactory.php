<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'reference' => Project::generateReference(),
            'client_id' => User::factory()->client(),
            'created_by' => null,
            'pipeline_stage_id' => PipelineStage::factory(),
            'title' => fake()->sentence(3),
            'brief' => fake()->paragraph(),
            'type' => fake()->randomElement(array_keys(Project::TYPES)),
            'status' => Project::STATUS_ACTIVE,
            'stage_changed_at' => now(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => Project::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
