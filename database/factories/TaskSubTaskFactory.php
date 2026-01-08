<?php

namespace Database\Factories;

use App\Models\TaskSubTask;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskSubTask>
 */
class TaskSubTaskFactory extends Factory
{
    protected $model = TaskSubTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtasks = [
            'Set up development environment',
            'Create database schema',
            'Implement user authentication',
            'Design UI mockups',
            'Write unit tests',
            'Review code',
            'Update documentation',
            'Perform security audit',
            'Optimize database queries',
            'Deploy to staging',
            'Fix reported bugs',
            'Implement API endpoints',
            'Add validation rules',
            'Create admin panel',
            'Set up CI/CD pipeline',
        ];

        return [
            'task_id' => Task::factory(),
            'title' => $this->faker->randomElement($subtasks),
            'is_completed' => $this->faker->boolean(60), // 60% chance of being completed
        ];
    }

    /**
     * Indicate that the subtask is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    /**
     * Indicate that the subtask is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
        ]);
    }
}
