<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Course;

class courseContoller extends Controller
{
    //
    public function getCourse(Request $request)
    {
        try {
            //code...
            $courses = Course::all();
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
}
