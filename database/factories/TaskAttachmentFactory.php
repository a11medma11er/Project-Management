<?php

namespace Database\Factories;

use App\Models\TaskAttachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskAttachment>
 */
class TaskAttachmentFactory extends Factory
{
    protected $model = TaskAttachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileTypes = [
            ['ext' => 'pdf', 'type' => 'application/pdf', 'size' => [500000, 5000000]],
            ['ext' => 'docx', 'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'size' => [100000, 2000000]],
            ['ext' => 'jpg', 'type' => 'image/jpeg', 'size' => [200000, 3000000]],
            ['ext' => 'png', 'type' => 'image/png', 'size' => [100000, 2000000]],
            ['ext' => 'txt', 'type' => 'text/plain', 'size' => [1000, 50000]],
        ];

        $fileType = $this->faker->randomElement($fileTypes);
        $fileName = $this->faker->words(2, true) . '.' . $fileType['ext'];

        return [
            'task_id' => Task::factory(),
            'file_name' => $fileName,
            'file_path' => 'tasks/attachments/' . $this->faker->uuid() . '.' . $fileType['ext'],
            'file_size' => $this->faker->numberBetween($fileType['size'][0], $fileType['size'][1]),
            'file_type' => $fileType['type'],
            'uploaded_by' => User::factory(),
        ];
    }

    /**
     * Screenshot image.
     */
    public function screenshot(): static
    {
        $fileName = 'screenshot-' . $this->faker->dateTime()->format('Y-m-d') . '.png';
        
        return $this->state(fn (array $attributes) => [
            'file_name' => $fileName,
            'file_path' => 'tasks/attachments/' . $this->faker->uuid() . '.png',
            'file_type' => 'image/png',
            'file_size' => $this->faker->numberBetween(100000, 2000000),
        ]);
    }
}
