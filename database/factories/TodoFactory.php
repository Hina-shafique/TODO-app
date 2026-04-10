<?php

namespace Database\Factories;

use App\Enum\TodoStatus;
use App\Enum\TodoPriority;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(TodoStatus::cases()),
            'priority' => $this->faker->randomElement(TodoPriority::cases()),
            'due_date' => $this->faker->dateTimeBetween(now(), '+1 month'),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TodoStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'status' => TodoStatus::PENDING,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TodoStatus::PENDING,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => TodoStatus::IN_PROGRESS,
            'completed_at' => null,
        ]);
    }
}
