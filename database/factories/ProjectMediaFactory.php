<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProjectMedia>
 */
class ProjectMediaFactory extends Factory
{
    protected $model = ProjectMedia::class;

    public function definition(): array
    {
        $name = fake()->slug(2).'.mp4';

        return [
            'project_id' => Project::factory(),
            'pipeline_stage_id' => null,
            'uploaded_by' => null,
            'source' => ProjectMedia::SOURCE_CLIENT,
            'kind' => ProjectMedia::KIND_SOURCE,
            'disk' => config('media.disk', 'local'),
            'path' => 'projects/'.Str::upper(Str::random(6))."/source/{$name}",
            'original_name' => $name,
            'mime_type' => 'video/mp4',
            'size' => fake()->numberBetween(1_000_000, 900_000_000),
            'visible_to_client' => true,
        ];
    }

    public function deliverable(): static
    {
        return $this->state(fn () => [
            'source' => ProjectMedia::SOURCE_TEAM,
            'kind' => ProjectMedia::KIND_DELIVERABLE,
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn () => [
            'source' => ProjectMedia::SOURCE_TEAM,
            'visible_to_client' => false,
        ]);
    }
}
