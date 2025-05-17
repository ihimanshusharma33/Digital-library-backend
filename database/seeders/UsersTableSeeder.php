<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Check if users already exist
        if (User::count() > 0) {
            $this->command->info('Users already seeded. Skipping...');
            return;
        }

        $users=[
            [
                'name' => 'Himanshu Sharma',
                'email' => 'Himanshusharma@gmail.com',
                'password' => bcrypt('himanshu123'),
                'role' => 'admin',
                'course_id' => 1,
                'library_id' => 123456,
            ],[
                'name'=>'Nayonika',
                'email'=>'nayonika@gmail.com',
                'password'=>bcrypt('nayonika123'),
                'role'=>'student',
                'university_roll_number'=>'123456789',
                'phone_number'=>'1234567890',
                'course_id'=>1,
                'library_id'=>123535,
            ],
            [
                'name'=>'Ravi Kumar',
                'email'=>'Ravi@gmail.com',
                'password'=>bcrypt('ravikumar123'),
                'role'=>'student',
                'university_roll_number'=>'123456789',
                'phone_number'=>'1234567890',
                'course_id'=>1,
                'library_id'=>123532,
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
