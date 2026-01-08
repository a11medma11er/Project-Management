<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\ProjectComment;
use App\Models\TaskComment;
use App\Models\AI\AIDecision;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Demo Data Seeding...');

        DB::transaction(function () {
            // Get existing users
            $users = User::all();
            
            if ($users->count() < 4) {
                $this->command->warn('⚠️  Not enough users. Please run DefaultUserSeeder first.');
                return;
            }

            $this->command->info('👥 Using ' . $users->count() . ' existing users');

            // ========================================
            // PHASE 1: Projects and Members
            // ========================================
            $this->command->info('📁 Creating Projects...');
            
            $projects = collect();
            
            // In Progress Projects (40%)
            $inProgressProjects = Project::factory()
                ->count(5)
                ->inProgress()
                ->create();
            $projects = $projects->merge($inProgressProjects);
            
            // Completed Projects (30%)
            $completedProjects = Project::factory()
                ->count(4)
                ->completed()
                ->create();
            $projects = $projects->merge($completedProjects);
            
            // On Hold Projects (30%)
            $onHoldProjects = Project::factory()
                ->count(3)
                ->onHold()
                ->create();
            $projects = $projects->merge($onHoldProjects);

            $this->command->info('✅ Created ' . $projects->count() . ' projects');

            // Attach members to projects
            $this->command->info('👨‍💼 Adding project members...');
            foreach ($projects as $project) {
                $memberCount = rand(3, 7);
                $projectUsers = $users->random(min($memberCount, $users->count()));
                
                foreach ($projectUsers as $user) {
                    $role = $this->faker()->randomElement(['Developer', 'Designer', 'Tester', 'Manager']);
                    
                    DB::table('project_members')->insert([
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'role' => $role,
                        'joined_at' => $this->faker()->dateTimeBetween('-6 months', 'now'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->command->info('✅ Project members assigned');

            // ========================================
            // PHASE 2: Tasks
            // ========================================
            $this->command->info('📋 Creating Tasks...');
            
            $tasks = collect();
            foreach ($projects as $project) {
                $taskCount = rand(3, 6);
                
                $projectTasks = Task::factory()
                    ->count($taskCount)
                    ->create([
                        'project_id' => $project->id,
                        'created_by' => $project->created_by,
                    ]);
                
                // Assign users to tasks
                foreach ($projectTasks as $task) {
                    $assignedCount = rand(1, 3);
                    $assignedUsers = $users->random(min($assignedCount, $users->count()));
                    
                    foreach ($assignedUsers as $user) {
                        DB::table('task_user')->insert([
                            'task_id' => $task->id,
                            'user_id' => $user->id,
                            'role' => $this->faker()->randomElement(['assignee', 'reviewer']),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                $tasks = $tasks->merge($projectTasks);
            }
            
            $this->command->info('✅ Created ' . $tasks->count() . ' tasks');

            // ========================================
            // PHASE 3: Comments
            // ========================================
            $this->command->info('💬 Adding Comments...');
            
            // Project Comments
            foreach ($projects->random(min(8, $projects->count())) as $project) {
                $commentCount = rand(2, 5);
                
                for ($i = 0; $i < $commentCount; $i++) {
                    $comment = ProjectComment::factory()->create([
                        'project_id' => $project->id,
                        'user_id' => $users->random()->id,
                    ]);
                    
                    // 30% chance of reply
                    if ($this->faker()->boolean(30)) {
                        ProjectComment::factory()->reply($comment)->create([
                            'user_id' => $users->random()->id,
                        ]);
                    }
                }
            }
            
            // Task Comments
            foreach ($tasks->random(min(25, $tasks->count())) as $task) {
                $commentCount = rand(1, 4);
                
                for ($i = 0; $i < $commentCount; $i++) {
                    $comment = TaskComment::factory()->create([
                        'task_id' => $task->id,
                        'user_id' => $users->random()->id,
                    ]);
                    
                    // 30% chance of reply
                    if ($this->faker()->boolean(30)) {
                        TaskComment::factory()->reply($comment)->create([
                            'user_id' => $users->random()->id,
                        ]);
                    }
                }
            }
            
            $this->command->info('✅ Comments added');

            // ========================================
            // PHASE 4: Task Details
            // ========================================
            $this->command->info('🏷️  Adding Task Tags, SubTasks, and Time Entries...');
            
            foreach ($tasks as $task) {
                // Tags (1-5 per task)
                $tagCount = rand(1, 5);
                for ($i = 0; $i < $tagCount; $i++) {
                    DB::table('task_tags')->insert([
                        'task_id' => $task->id,
                        'tag' => $this->faker()->randomElement([
                            'Frontend', 'Backend', 'Database', 'Bug', 'Feature', 
                            'Enhancement', 'Urgent', 'Review', 'Testing', 'UI', 'UX', 'API'
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // SubTasks (60% of tasks have subtasks)
                if ($this->faker()->boolean(60)) {
                    $subtaskCount = rand(2, 6);
                    for ($i = 0; $i < $subtaskCount; $i++) {
                        DB::table('task_sub_tasks')->insert([
                            'task_id' => $task->id,
                            'title' => $this->faker()->randomElement([
                                'Set up development environment',
                                'Create database schema',
                                'Implement user authentication',
                                'Design UI mockups',
                                'Write unit tests',
                                'Review code',
                                'Update documentation',
                            ]),
                            'is_completed' => $this->faker()->boolean(60),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                // Time Entries (for active tasks)
                if (in_array($task->status->value, ['in_progress', 'pending'])) {
                    $entryCount = rand(1, 3);
                    for ($i = 0; $i < $entryCount; $i++) {
                        $date = $this->faker()->dateTimeBetween('-1 month', 'now');
                        $durationMinutes = rand(30, 480);
                        $idleMinutes = rand(0, (int)($durationMinutes * 0.2)); // max 20% idle time
                        
                        DB::table('task_time_entries')->insert([
                            'task_id' => $task->id,
                            'user_id' => $users->random()->id,
                            'date' => $date->format('Y-m-d'),
                            'duration_minutes' => $durationMinutes,
                            'idle_minutes' => $idleMinutes,
                            'task_title' => $task->title,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            
            $this->command->info('✅ Task details added');

            // ========================================
            // PHASE 5: AI Decisions
            // ========================================
            $this->command->info('🤖 Creating AI Decisions...');
            
            $decisions = AIDecision::factory()
                ->count(20)
                ->create();
            
            $this->command->info('✅ Created ' . $decisions->count() . ' AI decisions');

            $this->command->info('');
            $this->command->info('🎉 Demo Data Seeding Completed Successfully!');
            $this->command->info('📊 Summary:');
            $this->command->info('   - Projects: ' . $projects->count());
            $this->command->info('   - Tasks: ' . $tasks->count());
            $this->command->info('   - AI Decisions: ' . $decisions->count());
        });
    }

    private function faker()
    {
        return \Faker\Factory::create();
    }
}
