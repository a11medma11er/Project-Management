<?php

namespace Database\Factories;

use App\Models\TaskTag;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskTag>
 */
class TaskTagFactory extends Factory
{
    protected $model = TaskTag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tags = [
            'Frontend',
            'Backend',
            'Database',
            'Bug',
            'Feature',
            'Enhancement',
            'Urgent',
            'Review',
            'Testing',
            'UI',
            'UX',
            'API',
            'Security',
            'Performance',
            'Documentation',
        ];

        return [
            'task_id' => Task::factory(),
            'tag' => $this->faker->randomElement($tags),
        ];
    }

    /**
     * Frontend tag.
     */
    public function frontend(): static
    {
        return $this->state(fn (array $attributes) => [
            'tag' => 'Frontend',
        ]);
    }

    /**
     * Backend tag.
     */
    public function backend(): static
    {
        return $this->state(fn (array $attributes) => [
            'tag' => 'Backend',
        ]);
    }

    /**
     * Bug tag.
     */
    public function bug(): static
    {
        return $this->state(fn (array $attributes) => [
            'tag' => 'Bug',
        ]);
    }

    /**
     * Urgent tag.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'tag' => 'Urgent',
        ]);
    }
}
