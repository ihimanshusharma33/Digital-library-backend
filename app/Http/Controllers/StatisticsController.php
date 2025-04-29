<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Course;
use App\Models\EBook;
use App\Models\Note;
use App\Models\Notification;
use App\Models\QuestionPaper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    public function getStatistics()
    {
        try {
            // Use cache to improve performance (cache for 5 minutes)
            $statistics = Cache::remember('system_statistics', 300, function () {
                // Count users
                $totalUsers = User::count();
                $verifiedUsers = User::whereNotNull('email_verified_at')->count();
                $studentUsers = User::where('role', 'student')->count();
                
                // Count resources by type
                $eBooks = EBook::count();
                $notes = Note::count();
                $questionPapers = QuestionPaper::count();
                
                // Calculate total digital resources
                $totalResources = $eBooks + $notes + $questionPapers;
                
                // Count books
                $totalBooks = Book::count();
                $physicalBooks = Book::where('is_available', true)->count();
                
                // Count courses
                $totalCourses = Course::count();
                
                // Count notifications/notices
                $totalNotices = Notification::count();
                $activeNotices = Notification::where('expires_at', '>', now())->count();
                
                return [
                    'users' => [
                        'total' => $totalUsers,
                        'verified' => $verifiedUsers,
                        'students' => $studentUsers
                    ],
                    'resources' => [
                        'total' => $totalResources,
                        'e_books' => $eBooks,
                        'notes' => $notes,
                        'question_papers' => $questionPapers
                    ],
                    'books' => [
                        'total' => $totalBooks,
                        'physical' => $physicalBooks
                    ],
                    'courses' => [
                        'total' => $totalCourses
                    ],
                    'notices' => [
                        'total' => $totalNotices,
                        'active' => $activeNotices
                    ]
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statistics
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
