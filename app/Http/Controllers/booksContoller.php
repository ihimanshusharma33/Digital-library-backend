<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Course;
use App\Models\IssuedBook; // Add this import to use the IssuedBook model
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class booksContoller extends Controller
{
    //
    public function getBooks(Request $request)
    {
        try {
            // Get query parameters
            $courseCode = $request->query('course_code');
            $semester = $request->query('semester');
            
            // Create a cache key based on query parameters
            $cacheKey = "books_" . ($courseCode ?? 'all') . "_" . ($semester ?? 'all');
            
            // Get data from cache or execute query (cache for 30 minutes)
            $books = Cache::remember($cacheKey, 1800, function () use ($courseCode, $semester) {
                // Start query builder
                $query = Book::query();
                
                // Apply filters if provided
                if ($courseCode) {
                    $query->where('course_code', $courseCode);
                }
                
                if ($semester) {
                    $query->where('semester', $semester);
                }
                
                // Select only needed fields and optimize query
                return $query->select([
                    'id', 'title', 'author', 'isbn', 'description', 
                    'publisher', 'publication_year', 'quantity', 
                    'available_quantity', 'shelf_location', 'category', 
                    'course_code', 'semester', 'is_available'
                ])->get();
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Books retrieved successfully',
                'data' => $books
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function addBooks(Request $request)
    {
        try {
            // Check if a file was uploaded
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Validate the file
                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid file upload'
                    ], 400);
                }
                
                // Create a new multipart form request to the file upload service
                $response = Http::attach(
                    'file', 
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post('https://file-upload-eaky.onrender.com/upload');
                
                // Check if the upload was successful
                if (!$response->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload file to server',
                        'error' => $response->body()
                    ], 500);
                }
                
                // Get the file URL from the response
                $responseData = $response->json();
                if (!isset($responseData['url'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid response from file server',
                        'error' => $responseData
                    ], 500);
                }
                
                // Set the file URL in the request data
                $request->merge(['file_path' => $responseData['url']]);
            }

            // Log the incoming course_code for debugging
            \Log::info('Received course_code: ' . $request->course_code);
            
            // First, check if the course exists - using trim to remove any whitespace
            $courseCode = trim($request->course_code);
            $course = Course::where('course_code', $courseCode)->first();
            
            // If not found with exact match, try case-insensitive search
            if (!$course) {
                $course = Course::whereRaw('LOWER(course_code) = ?', [strtolower($courseCode)])->first();
            }
            
            if (!$course) {
                // Log available courses for debugging
                $availableCourses = Course::pluck('course_code')->toArray();
                \Log::info('Available courses: ' . implode(', ', $availableCourses));
                
                return response()->json([
                
                    'available_codes' => $availableCourses // Show available codes in response
                ], 404);
            }
            
            // Check if semester is valid for this course
            if ($request->semester > $course->total_semesters || $request->semester < 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid semester. This course has ' . $course->total_semesters . ' semesters'
                ], 400);
            }
            
            // Proceed with book validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|max:13|unique:books,isbn',
                'description' => 'nullable|string',
                'publisher' => 'nullable|string|max:255',
                'publication_year' => 'nullable|integer',
                'quantity' => 'required|integer|min:0',
                'available_quantity' => 'required|integer|min:0|lte:quantity',
                'shelf_location' => 'nullable|string|max:50',
                'category' => 'nullable|string|max:100',
                'course_code' => 'required|string|exists:courses,course_code',
                'semester' => 'required|integer|min:1',
                'is_available' => 'boolean',
            ]);
            
            $book = Book::create($validatedData);
            
            // Clear cache for this course and semester
            $this->clearBookCache($book->course_code, $book->semester);
            
            return response()->json([
                'status' => true,
                'message' => 'Book added successfully',
                'data' => $book
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function updateBooks(Request $request, $id)
    {
        try {
            // Find the book
            $book = Book::find($id);
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }
            
            // Store original course code and semester for cache clearing
            $originalCourseCode = $book->course_code;
            $originalSemester = $book->semester;
            
            // Check if a file was uploaded
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Validate the file
                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid file upload'
                    ], 400);
                }
                
                // Create a new multipart form request to the file upload service
                $response = Http::attach(
                    'file', 
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post('https://file-upload-eaky.onrender.com/upload');
                
                // Check if the upload was successful
                if (!$response->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload file to server',
                        'error' => $response->body()
                    ], 500);
                }
                
                // Get the file URL from the response
                $responseData = $response->json();
                if (!isset($responseData['url'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid response from file server',
                        'error' => $responseData
                    ], 500);
                }
                
                // Set the file URL in the request data
                $request->merge(['file_path' => $responseData['url']]);
            }
            
            $book->update($request->all());
            
            // Clear cache for both original and new course/semester combinations
            $this->clearBookCache($originalCourseCode, $originalSemester);
            if ($request->has('course_code') || $request->has('semester')) {
                $this->clearBookCache($book->course_code, $book->semester);
            }
            
            // Clear the specific book's cache if it was cached individually
            Cache::forget("book_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Book updated successfully',
                'data' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function deleteBooks($id)
    {
        try {
            //code...
            $book = Book::find($id);
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }
            
            // Store course/semester before deletion to clear cache after
            $courseCode = $book->course_code;
            $semester = $book->semester;
            
            $book->delete();
            
            // Clear related caches
            $this->clearBookCache($courseCode, $semester);
            Cache::forget("book_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Book deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific book by ID with caching
     */
    public function getBook($id)
    {
        try {
            // Try to get from cache first (cache for 30 minutes)
            $book = Cache::remember("book_{$id}", 1800, function () use ($id) {
                return Book::find($id);
            });
            
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Book retrieved successfully',
                'data' => $book
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check book availability by ISBN, ID, title, or author
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:isbn,id,title,author',
                'value' => 'required|string',
            ]);
            
            $type = $request->type;
            $value = $request->value;
            
            $query = Book::query();
            
            switch ($type) {
                case 'isbn':
                    $query->where('isbn', $value);
                    break;
                case 'id':
                    $query->where('id', $value);
                    break;
                case 'title':
                    $query->where('title', 'like', '%' . $value . '%');
                    break;
                case 'author':
                    $query->where('author', 'like', '%' . $value . '%');
                    break;
            }
            
            // Get the books
            $books = $query->get();
            
            if ($books->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No books found matching the criteria'
                ], 404);
            }
            
            // Check availability for each book
            $booksWithAvailability = $books->map(function($book) {
                // A book is available if it's not currently issued or if all issued copies are returned
                $isAvailable = !IssuedBook::where('book_id', $book->id)
                    ->where('is_returned', false)
                    ->exists();
                
                $data = [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'is_available' => $isAvailable,
                ];
                
                // If book is available, include more details
                if ($isAvailable) {
                    $data = array_merge($data, [
                        'publisher' => $book->publisher,
                        'publication_year' => $book->publication_year,
                        'edition' => $book->edition,
                        'category' => $book->category,
                        'description' => $book->description,
                        // Add any other book attributes you want to include
                    ]);
                }
                
                return $data;
            });
            
            return response()->json([
                'status' => 'success',
                'data' => $booksWithAvailability,
                'message' => $booksWithAvailability->contains('is_available', true) 
                    ? 'Books available for checkout' 
                    : 'Sorry, no books are currently available for checkout'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check book availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Return a book that was previously issued
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnBook(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'issued_book_id' => 'required|exists:issued_books,id',
                'return_date' => 'required|date',
                'remarks' => 'nullable|string'
            ]);

            // Find the issued book record
            $issuedBook = IssuedBook::find($request->issued_book_id);
            
            if (!$issuedBook) {
                return response()->json([
                    'status' => false,
                    'message' => 'Issued book record not found'
                ], 404);
            }

            // Check if book is already returned
            if ($issuedBook->is_returned) {
                return response()->json([
                    'status' => false,
                    'message' => 'This book has already been returned'
                ], 400);
            }

            // Parse dates for fine calculation
            $dueDate = \Carbon\Carbon::parse($issuedBook->due_date);
            $returnDate = \Carbon\Carbon::parse($request->return_date);
            
            // Calculate fine if book is returned late (₹10 per day)
            $fineAmount = 0;
            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate);
                $fineAmount = $daysLate * 10; // ₹10 per day
            }

            // Update the issued book record
            $issuedBook->update([
                'return_date' => $request->return_date,
                'is_returned' => true,
                'fine_amount' => $fineAmount,
                'remarks' => $request->remarks ?? $issuedBook->remarks
            ]);

            // Reload the relationships for response
            $issuedBook->load(['book', 'user']);

            return response()->json([
                'status' => true,
                'message' => $fineAmount > 0 
                    ? "Book returned successfully. Fine amount: ₹{$fineAmount}" 
                    : "Book returned successfully",
                'data' => $issuedBook
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to process book return',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all books issued to a user with fine calculations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserIssuedBooks(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'library_id' => 'required|string',
            ]);

            // Find user by library ID
            $user = User::where('library_id', $request->library_id)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found with the provided library ID'
                ], 404);
            }

            // Get all books issued to this user
            $issuedBooks = IssuedBook::with('book')
                ->where('user_id', $user->id)
                ->orderBy('is_returned', 'asc')
                ->orderBy('due_date', 'asc')
                ->get();
            
            if ($issuedBooks->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No books have been issued to this user',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'library_id' => $user->library_id,
                            'email' => $user->email,
                        ],
                        'issued_books' => []
                    ]
                ], 200);
            }

            // Calculate current fines for unreturned books
            $today = \Carbon\Carbon::now()->startOfDay();
            $booksWithFines = $issuedBooks->map(function($issuedBook) use ($today) {
                $fineAmount = $issuedBook->fine_amount;
                $dueDate = \Carbon\Carbon::parse($issuedBook->due_date);
                $status = 'On Time';
                
                // Only calculate fine for books not returned yet
                if (!$issuedBook->is_returned) {
                    if ($today->gt($dueDate)) {
                        $daysLate = $today->diffInDays($dueDate);
                        $fineAmount = $daysLate * 10; // ₹10 per day
                        $status = 'Overdue';
                    } else {
                        $status = 'Issued';
                    }
                } else {
                    $status = 'Returned';
                    $returnDate = \Carbon\Carbon::parse($issuedBook->return_date);
                    if ($returnDate->gt($dueDate)) {
                        $status = 'Returned Late';
                    }
                }
                
                return [
                    'id' => $issuedBook->id,
                    'book_id' => $issuedBook->book_id,
                    'book_title' => $issuedBook->book->title,
                    'book_author' => $issuedBook->book->author,
                    'book_isbn' => $issuedBook->book->isbn,
                    'issue_date' => $issuedBook->issue_date,
                    'due_date' => $issuedBook->due_date,
                    'return_date' => $issuedBook->return_date,
                    'is_returned' => $issuedBook->is_returned,
                    'fine_amount' => $fineAmount,
                    'status' => $status,
                    'remarks' => $issuedBook->remarks,
                ];
            });

            // Calculate total fine
            $totalFine = $booksWithFines->sum('fine_amount');

            return response()->json([
                'status' => true,
                'message' => 'Books issued to user retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'library_id' => $user->library_id,
                        'email' => $user->email,
                    ],
                    'total_fine' => $totalFine,
                    'issued_books' => $booksWithFines
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve issued books',
                'error' => $e->getMessage()
            ], 500);
    }
}
    
    /**
     * Helper method to clear book cache
     */
    private function clearBookCache($courseCode = null, $semester = null)
    {
        // Clear course-specific cache
        if ($courseCode && $semester) {
            Cache::forget("books_{$courseCode}_{$semester}");
        }
        
        if ($courseCode) {
            Cache::forget("books_{$courseCode}_all");
        }
        
        if ($semester) {
            Cache::forget("books_all_{$semester}");
        }
        
        // Clear general books cache
        Cache::forget("books_all_all");
    }
}
