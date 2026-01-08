<?php

namespace Database\Factories;

use App\Models\ProjectAttachment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectAttachment>
 */
class ProjectAttachmentFactory extends Factory
{
    protected $model = ProjectAttachment::class;

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
            ['ext' => 'xlsx', 'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => [50000, 1000000]],
            ['ext' => 'jpg', 'type' => 'image/jpeg', 'size' => [200000, 3000000]],
            ['ext' => 'png', 'type' => 'image/png', 'size' => [100000, 2000000]],
            ['ext' => 'zip', 'type' => 'application/zip', 'size' => [1000000, 10000000]],
        ];

        $fileType = $this->faker->randomElement($fileTypes);
        $fileName = $this->faker->words(3, true) . '.' . $fileType['ext'];

        return [
            'project_id' => Project::factory(),
            'file_name' => $fileName,
            'file_path' => 'projects/attachments/' . $this->faker->uuid() . '.' . $fileType['ext'],
            'file_size' => $this->faker->numberBetween($fileType['size'][0], $fileType['size'][1]),
            'file_type' => $fileType['type'],
            'uploaded_by' => User::factory(),
        ];
    }

    /**
     * PDF document.
     */
    public function pdf(): static
    {
        $fileName = $this->faker->words(3, true) . '.pdf';
        
        return $this->state(fn (array $attributes) => [
            'file_name' => $fileName,
            'file_path' => 'projects/attachments/' . $this->faker->uuid() . '.pdf',
            'file_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(500000, 5000000),
        ]);
    }

    /**
     * Image file.
     */
    public function image(): static
    {
        $ext = $this->faker->randomElement(['jpg', 'png']);
        $fileName = $this->faker->words(2, true) . '.' . $ext;
        
        return $this->state(fn (array $attributes) => [
            'file_name' => $fileName,
            'file_path' => 'projects/attachments/' . $this->faker->uuid() . '.' . $ext,
            'file_type' => $ext === 'jpg' ? 'image/jpeg' : 'image/png',
            'file_size' => $this->faker->numberBetween(200000, 3000000),
        ]);
    }
}
