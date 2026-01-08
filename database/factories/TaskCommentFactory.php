<?php

namespace Database\Factories;

use App\Models\TaskComment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskComment>
 */
class TaskCommentFactory extends Factory
{
    protected $model = TaskComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Working on this task now.',
            'I need clarification on the requirements.',
            'This is more complex than initially thought.',
            'Almost done, just need to run final tests.',
            'Found a bug that needs to be fixed first.',
            'Can someone review my code?',
            'Updated the implementation based on feedback.',
            'This task is blocked by another dependency.',
            'I\'ve completed the initial draft.',
            'Need help with the database optimization.',
            'The API integration is working now.',
            'Added unit tests for this feature.',
        ];

        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'comment' => $this->faker->randomElement($comments),
            'parent_id' => null,
            'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    /**
     * Indicate that this is a reply to another comment.
     */
    public function reply(TaskComment $parent): static
    {
        $replies = [
            'Thanks for the clarification!',
            'I can help with that.',
            'Looks good to me.',
            'Let me check that for you.',
            'I agree, we should proceed this way.',
            'Good catch!',
            'I\'ll review this shortly.',
        ];

        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'task_id' => $parent->task_id,
            'comment' => $this->faker->randomElement($replies),
        ]);
    }
}
