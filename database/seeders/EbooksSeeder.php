<?php

namespace Database\Seeders;

use App\Models\EBook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EbooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ebooks=[
            [
                'title' => 'CS50 Lecture 1: C Programming',
                'description' => 'Introduction to C programming and compilers.',
                'author' => 'David J. Malan',
                'file_path' => 'https://cs50.harvard.edu/x/2025/notes/1/',
                'semester' => 1,
                'subject' => 'Programming Fundamentals',
                'course_id' => 1, // B.Tech CSE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Circuits and Electronics Lecture Notes',
                'description' => 'Fundamentals of electrical circuits and electronics.',
                'author' => 'MIT OpenCourseWare',
                'file_path' => 'https://ocw.mit.edu/courses/6-012-microelectronic-devices-and-circuits-fall-2005/pages/lecture-notes/',
                'semester' => 2,
                'subject' => 'Electrical Circuits',
                'course_id' => 2, // B.Tech EE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Strength of Materials',
                'description' => 'Comprehensive guide on material strength.',
                'author' => 'R. S. Khurmi',
                'file_path' => 'https://archive.org/stream/in.ernet.dli.2015.135076/2015.135076.Strength-Of-Materials-Ed3rd_djvu.txt',
                'semester' => 4,
                'subject' => 'Strength of Materials',
                'course_id' => 3, // B.Tech ME
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Digital Systems Lecture Notes',
                'description' => 'Introduction to digital systems and logic design.',
                'author' => 'MIT OpenCourseWare',
                'file_path' => 'https://ocw.mit.edu/courses/6-111-introductory-digital-systems-laboratory-spring-2006/pages/lecture-notes/',
                'semester' => 3,
                'subject' => 'Digital Electronics',
                'course_id' => 4, // B.Tech ECE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Environmental Engineering',
                'description' => 'Study material on environmental engineering concepts.',
                'author' => 'GATE Academy',
                'file_path' => 'https://www.gateacademy.co.in/exams/gate/civil-engineering/subjects/environmental-engineering-5',
                'semester' => 6,
                'subject' => 'Environmental Engineering',
                'course_id' => 5, // B.Tech CE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Principles of Management',
                'description' => 'Fundamentals of management principles.',
                'author' => 'OpenStax',
                'file_path' => 'https://assets.openstax.org/oscms-prodcms/media/documents/PrinciplesofManagement-OP_mGBMvoU.pdf',
                'semester' => 1,
                'subject' => 'Management',
                'course_id' => 6, // BBA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Business Communication for Success',
                'description' => 'Effective business communication strategies.',
                'author' => 'University of Minnesota',
                'file_path' => 'https://open.lib.umn.edu/businesscommunication/',
                'semester' => 2,
                'subject' => 'Business Communication',
                'course_id' => 7, // MBA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Introduction to Accounting',
                'description' => 'Basics of accounting principles.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/keac101.pdf',
                'semester' => 1,
                'subject' => 'Accounting',
                'course_id' => 8, // B.Com
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Introductory Microeconomics',
                'description' => 'Fundamentals of microeconomic theory.',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/67479/1/BLOCK1.pdf',
                'semester' => 2,
                'subject' => 'Economics',
                'course_id' => 9, // M.Com
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Physics Part I',
                'description' => 'Comprehensive physics textbook.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/keph101.pdf',
                'semester' => 1,
                'subject' => 'Physics',
                'course_id' => 10, // B.Sc
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Biotechnology and Its Applications',
                'description' => 'Insights into biotechnology applications.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/lebo110.pdf',
                'semester' => 4,
                'subject' => 'Biotechnology',
                'course_id' => 11, // M.Sc
                'created_at' => now(),

                'updated_at' => now(),
            ],
            [
                'title' => 'Data Structures and Algorithms Made Easy',
                'description' => 'Comprehensive guide on data structures and algorithms.',
                'author' => 'Narasimha Karumanchi',
                'file_path' => 'https://drive.google.com/file/d/1G2QNxy4ckBHA9wXqqmJXHfYySisZ6M6B/view',
                'semester' => 3,
                'course_id' => 12, // BCA
                'subject' => 'Data Structures',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Software Engineering',
                'description' => 'Principles and practices of software engineering.',
                'author' => 'Ian Sommerville',
                'file_path' => 'https://cs.gmu.edu/media/syllabi/Spring2025/CS_321SoundararajanS011.pdf',
                'semester' => 4,
                'course_id' => 13, // MCA
                'subject' => 'Software Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Entrepreneurship and Food Service Management',
                'description' => 'Guide on food service management and entrepreneurship.',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/90049/1/MFN-007%28English%29.pdf',
                'semester' => 2,
                'Subject' => 'Food Service Management',
                'course_id' => 14, // BHM
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Themes in Indian History I',
                'description' => 'Exploration of Indian historical themes.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/lehs1ps.pdf',
                'semester' => 1,
                'subject' => 'History',
                'course_id' => 15, // BA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Database System Concepts',
                'description' => 'Fundamentals of database systems, relational models, and SQL.',
                'author' => 'Abraham Silberschatz',
                'file_path' => 'https://ds.cs.ut.ee/stream/4e6cd723/db-textbook.pdf',
                'semester' => 3,
                'subject' => 'Database Management',
                'course_id' => 1, // B.Tech CSE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Basic Electrical Engineering',
                'description' => 'Basic concepts and laws of electrical circuits.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/leph103.pdf',
                'semester' => 1,
                'subject' => 'Basic Electrical Engineering',
                'course_id' => 2, // B.Tech EE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Thermodynamics for Engineers',
                'description' => 'Concepts of heat transfer and thermodynamic processes.',
                'author' => 'Yunus A. Cengel',
                'file_path' => 'https://www.nitc.ac.in/physics/syllabi/Thermodynamics_Yunus_Cengel.pdf',
                'semester' => 3,
                'subject' => 'Thermodynamics',
                'course_id' => 3, // B.Tech ME
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Signals and Systems',
                'description' => 'Understanding signals, systems, and Fourier analysis.',
                'author' => 'MIT OpenCourseWare',
                'file_path' => 'https://ocw.mit.edu/courses/6-003-signals-and-systems-fall-2011/resources/mit6_003f11_lec01/',
                'semester' => 3,
                'course_id' => 4, // B.Tech ECE
                'subject' => 'Electronics systems',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Surveying - I',
                'description' => 'Surveying concepts, leveling and measurement techniques.',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/68888/1/BTME-108-B1.pdf',
                'semester' => 2,
                'subject' => 'Surveying Engineering',
                'course_id' => 5, // B.Tech CE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Marketing Management',
                'description' => 'Introduction to marketing concepts and consumer behavior.',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/11729/1/Block-1.pdf',
                'semester' => 2,
                'subject' => 'Marketing',
                'course_id' => 6, // BBA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Financial Management',
                'description' => 'Fundamentals of corporate finance and budgeting.',
                'author' => 'OpenStax',
                'file_path' => 'https://openstax.org/books/financial-accounting/pages/1-introduction',
                'semester' => 3,
                'subject' => 'Finance',
                'course_id' => 7, // MBA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cost Accounting',
                'description' => 'Principles of cost management and analysis.',
                'author' => 'IGNOU',
                'subject'=>'Cost Accounting',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/49838/1/MCO-07-B1.pdf',
                'semester' => 2,
                'course_id' => 8, // B.Com
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Corporate Accounting',
                'description' => 'Accounting practices in corporate environments.',
                'author' => 'IGNOU',
                'subject' => 'Accounting',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/49990/1/MCO-06-B3.pdf',
                'semester' => 4,
                'course_id' => 9, // M.Com
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Inorganic Chemistry Part 1',
                'description' => 'NCERT-based Inorganic Chemistry concepts.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/kech101.pdf',
                'semester' => 1,
                'subject' => 'Inorganic Chemistry',
                'course_id' => 10, // B.Sc
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mathematical Methods',
                'description' => 'Advanced mathematical techniques for science students.',
                'author' => 'IGNOU',
                'subject' => 'Mathematics',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/80879/1/MMT-001B1E.pdf',
                'semester' => 1,
                'course_id' => 11, // M.Sc
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Computer Networks Notes',
                'description' => 'Basics of networking, protocols, and architecture.',
                'author' => 'Saylor Academy',
                'file_path' => 'https://learn.saylor.org/pluginfile.php/330/mod_resource/content/1/CS402-Computer-Networks.pdf',
                'semester' => 4,
                'course_id' => 12, // BCA
                'subject' => 'Networking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Advanced Java Programming',
                'description' => 'OOP concepts, multithreading, and GUI in Java.',
                'author' => 'IGNOU',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/49291/1/MCS-024B1.pdf',
                'semester' => 5,
                'subject' => 'Computer Science',
                'course_id' => 13, // MCA
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tourism and Hospitality Management',
                'description' => 'Introduction to tourism and hospitality industry.',
                'author' => 'IGNOU',
                'subject' => 'Tourism',
                'file_path' => 'https://egyankosh.ac.in/bitstream/123456789/74140/1/MTTM1E.pdf',
                'semester' => 2,
                'course_id' => 14, // BHM
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Indian Constitution at Work',
                'description' => 'Understanding of Indian constitution and political system.',
                'author' => 'NCERT',
                'file_path' => 'https://ncert.nic.in/textbook/pdf/leps1ps.pdf',
                'subject' => 'Political Science',
                'semester' => 1,
                'course_id' => 15, // BA
                'created_at' => now(),
                'updated_at' => now(),
            ]
        
        ];
        
        foreach ($ebooks as $ebook) {
            EBook::create($ebook);
        }
    }
}
