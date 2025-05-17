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
            
            "course_code"=> "BTECH-CSE-101",
            "course_name"=> "B.TECH CSE",
            "description"=> "Bachelor of technology in Computer Science and Engineering",
            "total_semesters"=> 8,
            "department"=> "Computer Science",
            "is_active"=> true
        ],[
            "course_code"=> "BTECH-EE-102",
            "course_name"=> "B.Tech EE",
            "description"=> "bachelor of technology in Electrical Engineering",
            "total_semesters"=> 8,
            "department"=> "Electrical",
            "is_active"=> true
        ],[
            
            "course_code"=> "BTECH-ME-103",
            "course_name"=> "BTECH ME",
            "description"=> "bachelor of Technology in Electrical Engineering",
            "total_semesters"=>8,
            "department"=> "Mechanical",
            "is_active"=> true
          ],[
            "course_code"=> "BTECH-ECE-104",
            "course_name"=> "BTECH ECE",
            "description"=> "Bachelor of Technology in Electronic and Electrical Engineering",
            "total_semesters"=> 8,
            "department"=> "Electrical",
            "is_active"=> true
          ],
          [
            "course_code"=> "BTECH-CE-105",
            "course_name"=> "BTECH CE",
            "description"=> "Bachelor of Technology in Civil Engineering",
            "total_semesters"=> 8,
            "department"=> "Civil",
            "is_active"=> true
          ],
          [
            "course_code"=> "BBA-201",
            "course_name"=> "BBA",
            "description"=>"Bachelor of bussiness Adminstration",
            "total_semesters"=> 8,
            "department"=> "Management",
            "is_active"=> true
          ],[
            "course_code"=> "MBA-202",
            "course_name"=> "MBA",
            "description"=> "Master of Bussiness Adminstration",
            "total_semesters"=> 4,
            "department"=> "Management",
            "is_active"=> true
          ],[
            "course_code"=>"B.COM-301",
            "course_name"=> "B.COM",
            "description"=> "Bachelor of Commerce",
            "total_semesters"=> 6,
            "department"=> "Commerce",
            "is_active"=> true
          ],[
            "course_code"=> "M.COM-302",
            "course_name"=> "M.COM",
            "description"=> "Master of Commerce",
            "total_semesters"=> 4,
            "department"=> "Commerce",
            "is_active"=> true
          ],[
            "course_code"=>"B.SC-401",
            "course_name"=> "B.SC ",
            "description"=> "Bachelor of Science",
            "total_semesters"=> 6,
            "department"=> "Science",
          ],[
            "course_code"=> "MSC-402",
            "course_name"=> "MSC",
            "description"=> "Master of Science",
            "total_semesters"=> 4,
            "department"=> "Science",
            "is_active"=> true
          ],[
            "course_code"=> "BCA-401",
            "course_name"=> "BCA",
            "description"=> "Bachelor of Computer Application",
            "total_semesters"=> 6,
            "department"=> "Computer Science",
            "is_active"=> true
          ],[
            "course_code"=> "MCA-402",
            "course_name"=> "MCA",
            "description"=> "Master of Computer Application",
            "total_semesters"=> 6,
            "department"=> "Computer Science",
            "is_active"=> true
          ],[
            "course_code"=> "BHM-501",
            "course_name"=> "BHM",
            "description"=> "Bachelor of Hotel Management",
            "total_semesters"=> 6,
            "department"=> "Hotel Management",
            "is_active"=> true
          ],[
            "course_code"=>"B.A-601",
            "course_name"=> "B.A",
            "description"=> "Bachelor of Arts",
            "total_semesters"=> 6,
            "department"=> "Arts",
            "is_active"=> true
          ]
        ];
        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
