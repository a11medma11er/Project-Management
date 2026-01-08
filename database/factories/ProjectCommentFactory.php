<?php

namespace Database\Factories;

use App\Models\ProjectComment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectComment>
 */
class ProjectCommentFactory extends Factory
{
    protected $model = ProjectComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Great progress on this project! Keep up the good work.',
            'We need to discuss the timeline for this milestone.',
            'The designs look fantastic. Well done team!',
            'Can we schedule a meeting to review the requirements?',
            'I have some concerns about the current approach.',
            'Excellent work on implementing the new features!',
            'We should prioritize this task for the next sprint.',
            'The client feedback has been very positive so far.',
            'Let\'s make sure we meet the deadline for this deliverable.',
            'I\'ve uploaded the updated documentation to the project folder.',
            'We need more resources for this phase of the project.',
            'The testing phase is going smoothly.',
        ];

        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'comment' => $this->faker->randomElement($comments),
            'parent_id' => null, // Top-level comment by default
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Indicate that this is a reply to another comment.
     */
    public function reply(ProjectComment $parent): static
    {
        $replies = [
            'I agree with your point.',
            'Thanks for the update!',
            'That makes sense.',
            'I will look into this.',
            'Good idea, let\'s implement that.',
            'I have some additional thoughts on this.',
            'Let me get back to you on that.',
        ];

        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'project_id' => $parent->project_id,
            'comment' => $this->faker->randomElement($replies),
        ]);
    }
}
