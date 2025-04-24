<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if notes already exist
        if (Note::count() > 0) {
            $this->command->info('Notes already seeded. Skipping...');
            return;
        }
        
        $notes = [
            [
                'title' => 'Data Structures Comprehensive Notes',
                'description' => 'Complete notes covering arrays, linked lists, stacks, and queues',
                'subject' => 'Data Structures',
                'author' => 'Prof. Jane Smith',
                'file_path' => 'notes/data_structures_notes.pdf',
                'course_code' => 'CSE101',
                'semester' => 3,
                'is_verified' => true,
            ],
            [
                'title' => 'Algorithm Analysis Notes',
                'description' => 'Detailed notes on time and space complexity analysis',
                'subject' => 'Algorithms',
                'author' => 'Prof. John Davis',
                'file_path' => 'notes/algorithm_analysis.pdf',
                'course_code' => 'CSE101',
                'semester' => 4,
                'is_verified' => true,
            ],
            [
                'title' => 'Digital Electronics Circuit Design',
                'description' => 'Notes on designing and analyzing digital circuits',
                'subject' => 'Digital Electronics',
                'author' => 'Dr. Emily Chen',
                'file_path' => 'notes/digital_electronics.pdf',
                'course_code' => 'ECE201',
                'semester' => 2,
                'is_verified' => true,
            ],
            [
                'title' => 'Semiconductor Physics',
                'description' => 'Notes on semiconductor materials and their properties',
                'subject' => 'Electronics',
                'author' => 'Prof. Michael Wong',
                'file_path' => 'notes/semiconductor_physics.pdf',
                'course_code' => 'ECE201',
                'semester' => 3,
                'is_verified' => true,
            ],
            [
                'title' => 'Thermodynamics Principles',
                'description' => 'Comprehensive notes on laws of thermodynamics',
                'subject' => 'Thermodynamics',
                'author' => 'Dr. Robert Johnson',
                'file_path' => 'notes/thermodynamics.pdf',
                'course_code' => 'ME301',
                'semester' => 4,
                'is_verified' => true,
            ],
            [
                'title' => 'Marketing Management',
                'description' => 'Notes on market analysis and strategy development',
                'subject' => 'Marketing',
                'author' => 'Prof. Sarah Wilson',
                'file_path' => 'notes/marketing_management.pdf',
                'course_code' => 'BBA101',
                'semester' => 1,
                'is_verified' => true,
            ],
            [
                'title' => 'Advanced Calculus Techniques',
                'description' => 'Detailed notes on advanced integration and differentiation',
                'subject' => 'Calculus',
                'author' => 'Dr. Thomas Brown',
                'file_path' => 'notes/advanced_calculus.pdf',
                'course_code' => 'MATH201',
                'semester' => 2,
                'is_verified' => true,
            ],
            [
                'title' => 'Linear Algebra Fundamentals',
                'description' => 'Notes on vector spaces, matrices, and linear transformations',
                'subject' => 'Linear Algebra',
                'author' => 'Prof. Jennifer Lee',
                'file_path' => 'notes/linear_algebra.pdf',
                'course_code' => 'MATH201',
                'semester' => 3,
                'is_verified' => true,
            ],
        ];

        foreach ($notes as $note) {
            Note::create($note);
        }
    }
}
