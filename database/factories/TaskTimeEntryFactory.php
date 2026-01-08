<?php

namespace Database\Factories;

use App\Models\TaskTimeEntry;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskTimeEntry>
 */
class TaskTimeEntryFactory extends Factory
{
    protected $model = TaskTimeEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $durationMinutes = $this->faker->numberBetween(30, 480); // 30 minutes to 8 hours
        $idleMinutes = $this->faker->numberBetween(0, (int)($durationMinutes * 0.2)); // max 20% idle

        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'duration_minutes' => $durationMinutes,
            'idle_minutes' => $idleMinutes,
            'task_title' => $this->faker->sentence(3),
        ];
    }

    /**
     * Indicate a short time entry (< 1 hour).
     */
    public function short(): static
    {
        $durationMinutes = $this->faker->numberBetween(15, 60);
        $idleMinutes = $this->faker->numberBetween(0, (int)($durationMinutes * 0.15));

        return $this->state(fn (array $attributes) => [
            'duration_minutes' => $durationMinutes,
            'idle_minutes' => $idleMinutes,
        ]);
    }

    /**
     * Indicate a long time entry (> 4 hours).
     */
    public function long(): static
    {
        $durationMinutes = $this->faker->numberBetween(240, 480);
        $idleMinutes = $this->faker->numberBetween(0, (int)($durationMinutes * 0.25));

        return $this->state(fn (array $attributes) => [
            'duration_minutes' => $durationMinutes,
            'idle_minutes' => $idleMinutes,
        ]);
    }
}
