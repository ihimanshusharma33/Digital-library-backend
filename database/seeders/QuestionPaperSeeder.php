<?php

namespace Database\Seeders;

use App\Models\QuestionPaper;
use Illuminate\Database\Seeder;

class QuestionPaperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if question papers already exist
        if (QuestionPaper::count() > 0) {
            $this->command->info('Question papers already seeded. Skipping...');
            return;
        }
        
        $questionPapers = [
            [
                'title' => 'Data Structures Mid-Term Exam',
                'subject' => 'Data Structures',
                'year' => 2024,
                'exam_type' => 'midterm',
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => '1',
                'semester' => 3,
                'description' => 'Mid-term examination covering arrays, linked lists, and stacks',
            ],
            [
                'title' => 'Algorithms Final Exam',
                'subject' => 'Algorithms',
                'year' => 2023,
                'exam_type' => 'final', // Changed from 'Final' to match enum
                'file_path' => 'qhttps://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 2,
                'semester' => 2,
                'description' => 'Final examination covering sorting, searching, and graph algorithms',
            ],
            [
                'title' => 'Digital Electronics Mid-Term',
                'subject' => 'Digital Electronics',
                'year' => 2024,
                'exam_type' => 'midterm', // Changed from 'Mid-Term' to match enum
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 4,
                'semester' => 2,
                'description' => 'Mid-term examination on logic gates and boolean algebra',
            ],
            [
                'title' => 'Circuit Analysis Final Exam',
                'subject' => 'Circuit Analysis',
                'year' => 2023,
                'exam_type' => 'final', // Changed from 'Final' to match enum
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 4,
                'semester' => 3,
                'description' => 'Final examination on Kirchhoff\'s laws and circuit theorems',
            ],
            [
                'title' => 'Thermodynamics Quiz',
                'subject' => 'Thermodynamics',
                'year' => 2024,
                'exam_type' => 'other', 
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 5,
                'semester' => 4,
                'description' => 'Quiz covering the first law of thermodynamics',
            ],
            [
                'title' => 'Business Ethics Final Exam',
                'subject' => 'Business Ethics',
                'year' => 2023,
                'exam_type' => 'final', 
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 7,
                'semester' => 1,
                'description' => 'Final examination on ethical business practices',
            ],
            [
                'title' => 'Calculus Mid-Term',
                'subject' => 'Calculus',
                'year' => 2024,
                'exam_type' => 'midterm', // Changed from 'Mid-Term' to match enum
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 2,
                'semester' => 2,
                'description' => 'Mid-term examination on differentiation and integration',
            ],
            [
                'title' => 'Supplementary Exam - Programming Basics',
                'subject' => 'Programming',
                'year' => 2024,
                'exam_type' => 'supplementary', // Added an example with 'supplementary' type
                'file_path' => 'https://tjtxvfcmlsdazavgxihx.supabase.co/storage/v1/object/public/Books//HimanshuSharma_resume.pdf',
                'course_id' => 1,
                'semester' => 1,
                'description' => 'Supplementary examination for programming fundamentals',
            ],
        ];

        foreach ($questionPapers as $paper) {
            QuestionPaper::create($paper);
        }
    }
}
