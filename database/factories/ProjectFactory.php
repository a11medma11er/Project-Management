<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'E-Commerce Platform Development',
            'Mobile Banking Application',
            'Healthcare Management System',
            'Real Estate Portal',
            'Educational Learning Platform',
            'Restaurant Booking System',
            'Inventory Management Software',
            'Social Media Dashboard',
            'CRM System Implementation',
            'HR Management Portal',
            'Travel Booking Platform',
            'Fitness Tracking App',
        ]);

        $skills = $this->faker->randomElements([
            'PHP', 'Laravel', 'React', 'Vue.js', 'Node.js', 
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'JavaScript', 'TypeScript', 'Python', 'Java',
            'HTML', 'CSS', 'Tailwind CSS', 'Bootstrap',
            'AWS', 'Docker', 'Git', 'API Development'
        ], $this->faker->numberBetween(3, 7));

        $categories = [
            'Web Development', 
            'Mobile App', 
            'Desktop Application',
            'Design', 
            'Data Analysis',
        ];

        $startDate = $this->faker->dateTimeBetween('-6 months', '-1 month');
        $deadline = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(4),
            'thumbnail' => 'projects/thumbnails/project-' . $this->faker->numberBetween(1, 10) . '.jpg',
            'description' => $this->faker->paragraphs(3, true),
            'priority' => $this->faker->randomElement(['High', 'Medium', 'Low']),
            'status' => $this->faker->randomElement(['Inprogress', 'Completed', 'On Hold']),
            'privacy' => $this->faker->randomElement(['Private', 'Team', 'Public']),
            'category' => $this->faker->randomElement($categories),
            'skills' => $skills,
            'deadline' => $deadline,
            'start_date' => $startDate,
            'progress' => $this->faker->numberBetween(0, 100),
            'is_favorite' => $this->faker->boolean(30),
            'team_lead_id' => User::inRandomOrder()->first()?->id,
            'created_by' => User::inRandomOrder()->first()?->id,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Inprogress',
            'progress' => $this->faker->numberBetween(10, 80),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Completed',
            'progress' => 100,
        ]);
    }

    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'On Hold',
            'progress' => $this->faker->numberBetween(0, 50),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'High',
        ]);
    }

    public function favorite(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => true,
        ]);
    }
}
