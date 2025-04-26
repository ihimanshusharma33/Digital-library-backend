<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionPaper;
use App\Models\Course;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class questionPaperContoller extends Controller
{
    //
    public function getQuestionPaper(Request $request)
    {
        try {
            // Get query parameters
            $courseCode = $request->query('course_code');
            $semester = $request->query('semester');
            $examType = $request->query('exam_type');
            
            // Create a cache key based on query parameters
            $cacheKey = "qp_" . ($courseCode ?? 'all') . "_" . ($semester ?? 'all') . "_" . ($examType ?? 'all');
            
            // Get data from cache or execute query (cache for 30 minutes)
            $questionPapers = Cache::remember($cacheKey, 1800, function () use ($courseCode, $semester, $examType) {
                // Start query builder
                $query = QuestionPaper::query();
                
                // Apply filters if provided
                if ($courseCode) {
                    $query->where('course_code', $courseCode);
                }
                
                if ($semester) {
                    $query->where('semester', $semester);
                }
                
                if ($examType) {
                    $query->where('exam_type', $examType);
                }
                
                // Select only needed fields for optimization
                return $query->select([
                    'id', 'title', 'subject', 'exam_type', 'year',
                    'file_path', 'course_code', 'semester', 'description', 
                    'created_at', 'updated_at'
                ])->get();
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Question papers retrieved successfully',
                'data' => $questionPapers
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function getOldQuestion(Request $request)
    {
        // Reuse the getQuestionPaper method for consistency
        return $this->getQuestionPaper($request);
    }
    
    public function addQuestionPaper(Request $request)
    {
        try {
            // Validate required fields first
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'exam_type' => 'required|string',
                'course_code' => 'required|string|exists:courses,course_code',
                'semester' => 'required|integer|min:1',
                'year' => 'required|integer',
                'subject' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

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
            $course = Course::where('course_code', $request->course_code)->first();
            
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

            // Create a new question paper with file path
            $questionPaper = new QuestionPaper();
            $questionPaper->title = $request->title;
            $questionPaper->content = $request->content;
            $questionPaper->file_path = $request->file_path;
            $questionPaper->exam_type = $request->exam_type;
            $questionPaper->course_code = $request->course_code;
            $questionPaper->semester = $request->semester;
            $questionPaper->year = $request->year;
            $questionPaper->subject = $request->subject;
            if ($request->has('description')) {
                $questionPaper->description = $request->description;
            }
            $questionPaper->save();

            // Clear relevant cache
            $this->clearQuestionPaperCache($questionPaper->course_code, $questionPaper->semester, $questionPaper->exam_type);

            return response()->json([
                'status' => true,
                'message' => 'Question paper added successfully',
                'data' => $questionPaper
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function addOldQuestion(Request $request)
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
            $course = Course::where('course_code', $request->course_code)->first();
            
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
            
            // Proceed with question paper validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'year' => 'required|integer',
                'exam_type' => 'required|string',
                'file_path' => 'required|string',
                'course_code' => 'required|string|exists:courses,course_code',
                'semester' => 'required|integer|min:1',
                'description' => 'nullable|string',
            ]);
            
            $questionPaper = QuestionPaper::create($validatedData);
            
            // Clear relevant cache
            $this->clearQuestionPaperCache($questionPaper->course_code, $questionPaper->semester, $questionPaper->exam_type);

            return response()->json([
                'status' => true,
                'message' => 'Question paper added successfully',
                'data' => $questionPaper
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateQuestionPaper(Request $request, $id)
    {
        try {
            // Find the question paper
            $questionPaper = QuestionPaper::find($id);
            if (!$questionPaper) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question paper not found'
                ], 404);
            }
            
            // Store original values for cache clearing
            $originalCourseCode = $questionPaper->course_code;
            $originalSemester = $questionPaper->semester;
            $originalExamType = $questionPaper->exam_type;
            
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
            
            $questionPaper->update($request->all());
            
            // Clear cache for both original and new values
            $this->clearQuestionPaperCache($originalCourseCode, $originalSemester, $originalExamType);
            
            // If any of these fields were changed, clear the cache for the new values too
            if ($request->has('course_code') || $request->has('semester') || $request->has('exam_type')) {
                $this->clearQuestionPaperCache(
                    $questionPaper->course_code, 
                    $questionPaper->semester, 
                    $questionPaper->exam_type
                );
            }
            
            // Clear specific question paper cache
            Cache::forget("question_paper_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Question paper updated successfully',
                'data' => $questionPaper
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function deleteQuestionPaper($id)
    {
        try {
            //code...
            $questionPaper = QuestionPaper::find($id);
            if (!$questionPaper) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question paper not found'
                ], 404);
            }
            
            // Store values for cache clearing before deletion
            $courseCode = $questionPaper->course_code;
            $semester = $questionPaper->semester;
            $examType = $questionPaper->exam_type;
            
            $questionPaper->delete();
            
            // Clear all relevant caches
            $this->clearQuestionPaperCache($courseCode, $semester, $examType);
            Cache::forget("question_paper_{$id}");
            
            return response()->json([
                'status' => true,
                'message' => 'Question paper deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    public function getQuestionPaperById($id)
    {
        try {
            // Try to get from cache first (cache for 30 minutes)
            $questionPaper = Cache::remember("question_paper_{$id}", 1800, function () use ($id) {
                return QuestionPaper::find($id);
            });
            
            if (!$questionPaper) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question paper not found'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Question paper retrieved successfully',
                'data' => $questionPaper
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
     * Helper method to clear question paper cache
     */
    private function clearQuestionPaperCache($courseCode = null, $semester = null, $examType = null)
    {
        // Clear course-specific cache
        if ($courseCode && $semester && $examType) {
            Cache::forget("qp_{$courseCode}_{$semester}_{$examType}");
        }
        
        // Clear partial caches
        if ($courseCode && $semester) {
            Cache::forget("qp_{$courseCode}_{$semester}_all");
        }
        
        if ($courseCode && $examType) {
            Cache::forget("qp_{$courseCode}_all_{$examType}");
        }
        
        if ($semester && $examType) {
            Cache::forget("qp_all_{$semester}_{$examType}");
        }
        
        if ($courseCode) {
            Cache::forget("qp_{$courseCode}_all_all");
        }
        
        if ($semester) {
            Cache::forget("qp_all_{$semester}_all");
        }
        
        if ($examType) {
            Cache::forget("qp_all_all_{$examType}");
        }
        
        // Clear general question papers cache
        Cache::forget("qp_all_all_all");
    }
}
