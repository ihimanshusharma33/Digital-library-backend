<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Comment out user creation as it's already been done
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Call our custom seeders
        $this->call([
            CourseSeeder::class,
            BookSeeder::class,
            QuestionPaperSeeder::class,
            NoteSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
