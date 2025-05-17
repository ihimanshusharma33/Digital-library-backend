<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Course;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NotesController extends Controller
{
    //
    public function getNotes(Request $request)
    {
        try {
            // Get query parameters
            $courseId = $request->query('course_id');
            $semester = $request->query('semester');
            
            // Create a cache key based on query parameters
            $cacheKey = "notes_" . ($courseId ?? 'all') . "_" . ($semester ?? 'all');
            
            // Get data from cache or execute query (cache for 30 minutes)
            $notes = Cache::remember($cacheKey, 1800, function () use ($courseId, $semester) {
                // Start query builder
                $query = Note::query();
                
                // Apply filters if provided
                if ($courseId) {
                    $query->where('course_id', $courseId);
                }
                
                if ($semester) {
                    $query->where('semester', $semester);
                }
                
                return $query->select([
                    'note_id', 'title', 'description', 'subject', 'author',
                    'file_path', 'course_id', 'semester',
                    'created_at', 'updated_at'
                ])->get();
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Notes retrieved successfully',
                'data' => $notes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function addNotes(Request $request)
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
            } else {
                // If no file is provided
                return response()->json([
                    'status' => false,
                    'message' => 'File upload is required'
                ], 400);
            }
            
            // First, check if the course exists
            $course = Course::where('course_id', $request->course_id)->first();
            
            if (!$course) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not found with the provided course code'
                ], 404);
            }
            
            // Check if semester is valid for this course
            if ($request->semester > $course->total_semesters || $request->semester < 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid semester. This course has ' . $course->total_semesters . ' semesters'
                ], 400);
            }
            
            // Proceed with note validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'subject' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'file_path' => 'required|string|max:255',
                'course_id' => 'required|string|exists:courses,course_id',
                'semester' => 'required|integer|min:1'
            ]);
            
            $note = Note::create($validatedData);

            // Clear cache for this course and semester
            $this->clearNoteCache($note->course_id, $note->semester);

            return response()->json([
                'status' => true,
                'message' => 'Note added successfully',
                'data' => $note
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function updateNotes(Request $request, $id)
    {
        try {
            // Find the note
            $note = Note::find($id);
            if (!$note) {
                return response()->json([
                    'status' => false,
                    'message' => 'Note not found'
                ], 404);
            }
            
            // Store original course code and semester for cache clearing
            $originalcourseId = $note->course_id;
            $originalSemester = $note->semester;
            
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
            
            $note->update($request->all());
            
            // Clear cache for both original and new course/semester combinations
            $this->clearNoteCache($originalcourseId, $originalSemester);
            if ($request->has('course_id') || $request->has('semester')) {
                $this->clearNoteCache($note->course_id, $note->semester);
            }
            
            // Clear single note cache
            Cache::forget("note_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Note updated successfully',
                'data' => $note
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function deleteNotes($id)
    {
        try {
            //code...
            $note = Note::find($id);
            if (!$note) {
                return response()->json([
                    'status' => false,
                    'message' => 'Note not found'
                ], 404);
            }
            
            // Store course/semester before deletion to clear cache after
            $courseId = $note->course_id;
            $semester = $note->semester;
            
            $note->delete();
            
            // Clear relevant caches
            $this->clearNoteCache($courseId, $semester);
            Cache::forget("note_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Note deleted successfully'
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
     * Get a specific note by ID with caching
     */
    public function getNote($id)
    {
        try {
            // Try to get from cache first (cache for 30 minutes)
            $note = Cache::remember("note_{$id}", 1800, function () use ($id) {
                return Note::find($id);
            });
            
            if (!$note) {
                return response()->json([
                    'status' => false,
                    'message' => 'Note not found'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Note retrieved successfully',
                'data' => $note
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
     * Helper method to clear note cache
     */
    private function clearNoteCache($courseId = null, $semester = null)
    {
        // Clear course-specific cache
        if ($courseId && $semester) {
            Cache::forget("notes_{$courseId}_{$semester}");
        }
        
        if ($courseId) {
            Cache::forget("notes_{$courseId}_all");
        }
        
        if ($semester) {
            Cache::forget("notes_all_{$semester}");
        }
        
        // Clear general notes cache
        Cache::forget("notes_all_all");
    }
}
