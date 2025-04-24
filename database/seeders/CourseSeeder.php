<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if courses already exist
        if (Course::count() > 0) {
            $this->command->info('Courses already seeded. Skipping...');
            return;
        }
        
        $courses = [
            [
                'course_code' => 'CSE101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Fundamentals of computer science and programming',
                'total_semesters' => 8,
                'department' => 'Computer Science',
                'is_active' => true,
            ],
            [
                'course_code' => 'ECE201',
                'course_name' => 'Electronics Engineering',
                'description' => 'Basic principles of electronics and circuit design',
                'total_semesters' => 8,
                'department' => 'Electronics',
                'is_active' => true,
            ],
            [
                'course_code' => 'ME301',
                'course_name' => 'Mechanical Engineering',
                'description' => 'Study of mechanical systems and thermodynamics',
                'total_semesters' => 8,
                'department' => 'Mechanical',
                'is_active' => true,
            ],
            [
                'course_code' => 'BBA101',
                'course_name' => 'Business Administration',
                'description' => 'Fundamentals of business management and administration',
                'total_semesters' => 6,
                'department' => 'Business',
                'is_active' => true,
            ],
            [
                'course_code' => 'MATH201',
                'course_name' => 'Advanced Mathematics',
                'description' => 'Higher mathematics including calculus and linear algebra',
                'total_semesters' => 6,
                'department' => 'Mathematics',
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
