<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if books already exist
        if (Book::count() > 0) {
            $this->command->info('Books already seeded. Skipping...');
            return;
        }
        
        $books = [
            [
                'title' => 'Introduction to Algorithms',
                'author' => 'Thomas H. Cormen',
                'isbn' => '9780262033848',
                'description' => 'Comprehensive introduction to algorithms',
                'publisher' => 'MIT Press',
                'publication_year' => 2009,
                'quantity' => 15,
                'available_quantity' => 10,
                'shelf_location' => 'A1-S3',
                'category' => 'Computer Science',
                'course_code' => 'CSE101',
                'semester' => 3,
                'is_available' => true,
            ],
            [
                'title' => 'Digital Electronics',
                'author' => 'William Stallings',
                'isbn' => '9781259025471',
                'description' => 'Fundamentals of digital electronics and circuit design',
                'publisher' => 'Pearson',
                'publication_year' => 2017,
                'quantity' => 12,
                'available_quantity' => 8,
                'shelf_location' => 'B2-S1',
                'category' => 'Electronics',
                'course_code' => 'ECE201',
                'semester' => 2,
                'is_available' => true,
            ],
            [
                'title' => 'Thermodynamics: An Engineering Approach',
                'author' => 'Yunus A. Cengel',
                'isbn' => '9780073398174',
                'description' => 'Comprehensive study of thermodynamics for engineers',
                'publisher' => 'McGraw-Hill Education',
                'publication_year' => 2014,
                'quantity' => 10,
                'available_quantity' => 6,
                'shelf_location' => 'C3-S2',
                'category' => 'Mechanical Engineering',
                'course_code' => 'ME301',
                'semester' => 4,
                'is_available' => true,
            ],
            [
                'title' => 'Principles of Management',
                'author' => 'Peter F. Drucker',
                'isbn' => '9780062836489',
                'description' => 'Essential guide to business management principles',
                'publisher' => 'Harper Business',
                'publication_year' => 2019,
                'quantity' => 20,
                'available_quantity' => 15,
                'shelf_location' => 'D1-S4',
                'category' => 'Business',
                'course_code' => 'BBA101',
                'semester' => 1,
                'is_available' => true,
            ],
            [
                'title' => 'Advanced Calculus',
                'author' => 'James Stewart',
                'isbn' => '9781285741550',
                'description' => 'In-depth exploration of calculus concepts',
                'publisher' => 'Cengage Learning',
                'publication_year' => 2015,
                'quantity' => 8,
                'available_quantity' => 5,
                'shelf_location' => 'E2-S3',
                'category' => 'Mathematics',
                'course_code' => 'MATH201',
                'semester' => 2,
                'is_available' => true,
            ],
            [
                'title' => 'Data Structures and Algorithms',
                'author' => 'Robert Sedgewick',
                'isbn' => '9780321573513',
                'description' => 'Comprehensive guide to data structures and algorithms',
                'publisher' => 'Addison-Wesley',
                'publication_year' => 2011,
                'quantity' => 18,
                'available_quantity' => 12,
                'shelf_location' => 'A2-S2',
                'category' => 'Computer Science',
                'course_code' => 'CSE101',
                'semester' => 4,
                'is_available' => true,
            ],
            [
                'title' => 'Microelectronic Circuits',
                'author' => 'Adel S. Sedra',
                'isbn' => '9780199339136',
                'description' => 'Analysis and design of electronic circuits',
                'publisher' => 'Oxford University Press',
                'publication_year' => 2020,
                'quantity' => 14,
                'available_quantity' => 9,
                'shelf_location' => 'B3-S4',
                'category' => 'Electronics',
                'course_code' => 'ECE201',
                'semester' => 3,
                'is_available' => true,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
