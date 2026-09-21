<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'client_message' => fake()->sentence(),
            'position' => fake()->numberBetween(1, 20),
            'color' => fake()->randomElement(['slate', 'amber', 'blue', 'violet', 'emerald', 'rose']),
            'is_default' => false,
            'is_final' => false,
            'notifies_client' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ['is_default' => true, 'position' => 1]);
    }

    public function final(): static
    {
        return $this->state(fn () => ['is_final' => true, 'position' => 99]);
    }

    public function silent(): static
    {
        return $this->state(fn () => ['notifies_client' => false]);
    }
}
