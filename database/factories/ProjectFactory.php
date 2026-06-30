<?php

namespace Database\Factories;

use App\Enum\ProjectStatus;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => ucwords(fake()->words(3, true)),
            'description' => fake()->optional()->sentence(),
            'status' => ProjectStatus::ACTIVE,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+3 months'),
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => ProjectStatus::COMPLETED]);
    }

    public function archived(): static
    {
        return $this->state(['status' => ProjectStatus::ARCHIVED]);
    }
}
