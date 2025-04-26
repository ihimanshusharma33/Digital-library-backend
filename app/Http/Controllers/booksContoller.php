<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Course;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
