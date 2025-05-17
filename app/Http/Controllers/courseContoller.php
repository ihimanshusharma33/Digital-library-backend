<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class courseContoller extends Controller
{
    //
    public function getCourse(Request $request)
    {
        try {
            // Create a cache key for all courses
            $cacheKey = "all_courses";
            
            // Try to get data from cache first (cache for 1 hour as course data changes less frequently)
            $courses = Cache::remember($cacheKey, 3600, function () {
                return Course::select([
                    'course_id', 'course_code', 'course_name', 'description', 
                    'total_semesters', 'department', 'is_active'
                ])->get();
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Courses retrieved successfully',
                'data' => $courses
            ], 200); // 200 = OK
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function addCourse(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'course_code' => 'required|string|max:50|unique:courses,course_code',
                'course_name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'total_semesters' => 'required|integer|min:1',
                'department' => 'required|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 = Validation Error
            }
    
            $course = Course::create([
                'course_code' => $request->course_code,
                'course_name' => $request->course_name,
                'description' => $request->description,
                'total_semesters' => $request->total_semesters,
                'department' => $request->department,
                'is_active' => true,
            ]);
            
            // Clear course cache
            Cache::forget('all_courses');
    
            return response()->json([
                'status' => true,
                'message' => 'Course created successfully',
                'data' => $course
            ], 201); // 201 = Created
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a course
     */
    public function updateCourse(Request $request, $id)
    {
        try {
            $course = Course::find($id);
            
            if (!$course) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not found'
                ], 404);
            }
            
            // Store original course code for cache clearing
            $originalcourseId = $course->course_id;
            
            $validator = Validator::make($request->all(), [
                'course_code' => [
                    'sometimes', 
                    'string', 
                    'max:50',
                    // Specify the course_id column explicitly
                    Rule::unique('courses')->ignore($id, 'course_id'),
                ],
                'course_name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'total_semesters' => 'sometimes|integer|min:1',
                'department' => 'sometimes|string|max:255',
                'is_active' => 'sometimes|boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $course->update($request->all());
            
            // Clear all relevant caches
            Cache::forget('all_courses');
            Cache::forget("course_{$originalcourseId}");
            if ($request->has('course_code') && $originalcourseId !== $request->course_code) {
                Cache::forget("course_{$request->course_code}");
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Course updated successfully',
                'data' => $course
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
     * Delete a course
     */
    public function deleteCourse($id)
    {
        try {
            $course = Course::find($id);
            
            if (!$course) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not found'
                ], 404);
            }
            
            // Store course code before deletion for cache clearing
            $courseId = $course->course_code;
            
            $course->delete();
            
            // Clear caches
            Cache::forget('all_courses');
            Cache::forget("course_{$courseId}");
            
            return response()->json([
                'status' => true,
                'message' => 'Course deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getCourseById($id)
    {
        try {
            $course = Course::find($id); 
            
            if (!$course) {
                return response()->json([
                    'status' => false,
                    'message' => 'Course not found'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Course retrieved successfully',
                'data' => $course
            ], 200); // 200 = OK

            // Rest of your method
        } catch (\Exception $e) {
            // Error handling
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
