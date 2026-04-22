<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'preferred_hours' => '09:00-18:00',
                'break_duration' => 60,
            ]
        );

        $catTravail = \App\Models\Category::firstOrCreate(['name' => 'Travail', 'user_id' => $user->id]);
        $catSport = \App\Models\Category::firstOrCreate(['name' => 'Sport', 'user_id' => $user->id]);

        \App\Models\Availability::firstOrCreate([
            'user_id' => $user->id,
            'day' => 'Mon',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        \App\Models\Availability::firstOrCreate([
            'user_id' => $user->id,
            'day' => 'Tue',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        \App\Models\Task::firstOrCreate([
            'user_id' => $user->id,
            'category_id' => $catTravail->id,
            'title' => 'Terminer le projet',
            'description' => 'Finaliser la v1 de SmartSchedule',
            'duration' => 120,
            'priority' => 'high',
            'deadline' => now()->addDays(2),
            'status' => 'todo',
        ]);

        \App\Models\Task::firstOrCreate([
            'user_id' => $user->id,
            'category_id' => $catSport->id,
            'title' => 'Séance de course à pied',
            'description' => '10km au parc',
            'duration' => 60,
            'priority' => 'medium',
            'deadline' => now()->addDays(1),
            'status' => 'todo',
        ]);
    }
}
