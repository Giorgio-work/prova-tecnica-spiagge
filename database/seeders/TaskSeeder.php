<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users first if they don't exist
        $users = User::all();
        if ($users->count() < 5) {
            $users = User::factory(5)->create();
        }

        // Create various types of tasks
        $taskData = [
            [
                'title' => 'Setup beach umbrellas for summer season',
                'description' => 'Prepare and install all beach umbrellas for the upcoming summer season. Check for damages and replace if necessary.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Update beach reservation system',
                'description' => 'Implement new features for the online reservation system including payment integration and mobile responsiveness.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'due_date' => now()->addDays(14),
            ],
            [
                'title' => 'Clean and maintain beach facilities',
                'description' => 'Daily cleaning and maintenance of all beach facilities including restrooms, showers, and common areas.',
                'status' => 'completed',
                'priority' => 'medium',
                'due_date' => now()->subDays(1),
                'completed_at' => now()->subHours(2),
            ],
            [
                'title' => 'Inventory check for beach equipment',
                'description' => 'Conduct a comprehensive inventory check of all beach equipment including sunbeds, umbrellas, and water sports gear.',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Staff training for customer service',
                'description' => 'Organize training sessions for beach staff to improve customer service and handle reservations efficiently.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(10),
            ],
            [
                'title' => 'Marketing campaign for summer promotions',
                'description' => 'Create and launch marketing campaigns for summer promotions and special packages.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => now()->addDays(21),
            ],
            [
                'title' => 'Safety equipment inspection',
                'description' => 'Inspect all safety equipment including life jackets, first aid kits, and emergency communication devices.',
                'status' => 'completed',
                'priority' => 'urgent',
                'due_date' => now()->subDays(2),
            ],
            [
                'title' => 'Beach area zoning and layout planning',
                'description' => 'Plan the optimal layout for beach areas including VIP sections, family areas, and sports zones.',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(30),
            ],
        ];

        foreach ($taskData as $data) {
            $user = $users->random();

            Task::create(array_merge($data, [
                'user_id' => $user->id,
            ]));
        }

        // Create additional random tasks
        Task::factory(20)->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Create some overdue tasks
        Task::factory(5)->overdue()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Create some high priority tasks
        Task::factory(8)->highPriority()->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // Create some urgent tasks
        Task::factory(3)->urgent()->create([
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
