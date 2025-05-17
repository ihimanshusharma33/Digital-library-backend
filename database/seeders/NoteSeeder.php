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
        $notes=[
            [
                'title' => 'Introduction to Programming',
                'description' => 'Basic concepts of programming using C.',
                'subject' => 'Programming in C',
                'author' => 'John Doe',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/keip101.pdf',
                'course_id' => 1, // B.Tech CSE
                'semester' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Digital Electronics Notes',
                'description' => 'Fundamentals of logic gates, flip-flops, and digital circuits.',
                'subject' => 'Digital Electronics',
                'author' => 'Jane Smith',
                'file_path' => 'https://dspace.mit.edu/bitstream/handle/1721.1/117308/MIT6_111F17_lec01.pdf',
                'course_id' => 4, // B.Tech ECE
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Accounting Principles',
                'description' => 'Basic principles and concepts of accounting.',
                'subject' => 'Financial Accounting',
                'author' => 'Dr. Meena R.',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/11858/1/Unit-1.pdf',
                'course_id' => 8, // B.Com
                'semester' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Organizational Behavior',
                'description' => 'Study of human behavior in an organization.',
                'subject' => 'Management Studies',
                'author' => 'Dr. Ramesh S.',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/58820/1/MS-21.pdf',
                'course_id' => 6, // BBA
                'semester' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Environmental Science Notes',
                'description' => 'Introduction to environmental studies and sustainability.',
                'subject' => 'Environmental Science',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/iesc1dd.zip',
                'course_id' => 10, // B.Sc
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Operating System Concepts',
                'description' => 'Processes, threads, scheduling, and memory management.',
                'subject' => 'Operating System',
                'author' => 'Silberschatz',
                'file_path' => 'https://cs.wmich.edu/gupta/os/notes/OSBook.pdf',
                'course_id' => 12, // BCA
                'semester' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Compiler Design Notes',
                'description' => 'Lexical analysis, parsing, syntax trees, and code generation.',
                'subject' => 'Compiler Design',
                'author' => 'Dr. S. Patel',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/106108113/pdf/M3L01.pdf',
                'course_id' => 13, // MCA
                'semester' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Food Safety Guidelines',
                'description' => 'Basic hygiene and food safety principles.',
                'subject' => 'Food & Nutrition',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/26889/1/Unit-1.pdf',
                'course_id' => 14, // BHM
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Constitutional Law Notes',
                'description' => 'Overview of Indian Constitution and its key features.',
                'subject' => 'Indian Constitution',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/leps1ps.pdf',
                'course_id' => 15, // BA
                'semester' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Data Structures using C++',
                'description' => 'Comprehensive notes on arrays, stacks, queues, linked lists, and trees.',
                'subject' => 'Data Structures',
                'author' => 'Prof. R. Krishnan',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/106102064/DS_Notes.pdf',
                'course_id' => 1, // B.Tech CSE
                'semester' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Fluid Mechanics Lecture Notes',
                'description' => 'Detailed coverage on properties of fluids, pressure measurement, and flow.',
                'subject' => 'Fluid Mechanics',
                'author' => 'Prof. S.K. Som',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/112105171/Fluid_Mechanics_Notes.pdf',
                'course_id' => 3, // B.Tech ME
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Network Analysis',
                'description' => 'Circuit theory, mesh & nodal analysis, Thevenin’s and Norton’s theorems.',
                'subject' => 'Basic Electrical Engineering',
                'author' => 'Dr. M.P. Desai',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/108105053/Basic_EE_Notes.pdf',
                'course_id' => 2, // B.Tech EE
                'semester' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Corporate Finance Notes',
                'description' => 'Concepts of capital budgeting, risk analysis, and capital structure.',
                'subject' => 'Corporate Finance',
                'author' => 'Dr. J. Gupta',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/58602/1/MS-42.pdf',
                'course_id' => 7, // MBA
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Linear Algebra Essentials',
                'description' => 'Matrices, vector spaces, eigenvalues, and eigenvectors.',
                'subject' => 'Linear Algebra',
                'author' => 'MIT OCW',
                'file_path' => 'https://ocw.mit.edu/courses/mathematics/18-06-linear-algebra-spring-2010/lecture-notes/MIT18_06S10_chapter1.pdf',
                'course_id' => 10, // B.Sc
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Database Management Systems',
                'description' => 'Relational databases, normalization, SQL, and transactions.',
                'subject' => 'DBMS',
                'author' => 'R. Elmasri',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/106105175/DBMS_Notes.pdf',
                'course_id' => 12, // BCA
                'semester' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Advanced Software Engineering',
                'description' => 'UML, Agile, risk management, and software metrics.',
                'subject' => 'Software Engineering',
                'author' => 'Dr. Ritu T.',
                'file_path' => 'https://nptel.ac.in/content/storage2/courses/106105087/SoftEng_Notes.pdf',
                'course_id' => 13, // MCA
                'semester' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Hospitality Operations Basics',
                'description' => 'Front office, housekeeping, food & beverage operations.',
                'subject' => 'Hotel Operations',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/30098/1/Unit-1.pdf',
                'course_id' => 14, // BHM
                'semester' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Political Theory Essentials',
                'description' => 'Ideas of liberty, justice, rights, and democracy.',
                'subject' => 'Political Science',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/leps1ps.pdf',
                'course_id' => 15, // BA
                'semester' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ];

        foreach ($notes as $note) {
            Note::create($note);
        }
    }
}
