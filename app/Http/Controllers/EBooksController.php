<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EBook;
use App\Models\Course;
use Illuminate\Support\Facades\Http;

class EBooksController extends Controller
{
    public function getEBooks(Request $request)
    {
        try {
            $courseId = $request->query('course_id');
            $semester = $request->query('semester');

            // Validate the query parameters
            $query = EBook::query();

            // Apply filters if provided
            if ($courseId) {
                $query->where('course_id', $courseId);
            }

            if ($semester) {
                $query->where('semester', $semester);
            }

            // Get filtered results
            $ebooks = $query->get();


            return response()->json([
                'status' => true,
                'message' => 'E-Books retrieved successfully',
                'data' => $ebooks
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function addEBook(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'author' => 'required|string|max:255',
                'course_id' => 'nullable|string|exists:courses,course_id',
                'semester' => 'nullable|integer|min:1',
                'subject' => 'required|string|max:255',
            ]);

            // First, check if the course exists
            if ($request->has('course_id')) {
                $course = Course::where('course_id', $request->course_id)->first();

                if (!$course) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Course not found with the provided course code'
                    ], 404);
                }

                // Check if semester is valid for this course
                if (
                    $request->has('semester') &&
                    ($request->semester > $course->total_semesters || $request->semester < 1)
                ) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid semester. This course has ' . $course->total_semesters . ' semesters'
                    ], 400);
                }
            }

            // Proceed with e-book validation


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
                $filePath = Http::attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post('https://file-upload-eaky.onrender.com/upload');

                // Check if the upload was successful
                if (!$filePath->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to upload file to server',
                        'error' => $filePath->body()
                    ], 500);
                }

                // Get the file URL from the response
                $responseData = $filePath->json();
                if (!isset($filePath['url'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid response from file server',
                        'error' => $filePath
                    ], 500);
                }

                // Set the file URL in the request data
                $request->merge(['file_path' => $filePath['url']]);
            } else {
                // If no file is provided
                return response()->json([
                    'status' => false,
                    'message' => 'File upload is required'
                ], 400);
            }
            // adding File Path to validated data

            $validatedData['file_path'] = $request->file_path;

            $ebook = EBook::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'E-Book added successfully',
                'data' => $ebook
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateEBook(Request $request, $id)
    {
        try {
            // Find the e-book
            $ebook = EBook::find($id);
            if (!$ebook) {
                return response()->json([
                    'status' => false,
                    'message' => 'E-Book not found'
                ], 404);
            }

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

            // If course code is being updated, validate it
            if ($request->has('course_code') && $request->course_code != $ebook->course_code) {
                $course = Course::where('course_code', $request->course_code)->first();

                if (!$course) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Course not found with the provided course code'
                    ], 404);
                }

                // Check if semester is valid for this course
                if (
                    $request->has('semester') &&
                    ($request->semester > $course->total_semesters || $request->semester < 1)
                ) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid semester. This course has ' . $course->total_semesters . ' semesters'
                    ], 400);
                }
            }

            $ebook->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'E-Book updated successfully',
                'data' => $ebook
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteEBook($id)
    {
        try {
            $ebook = EBook::find($id);
            if (!$ebook) {
                return response()->json([
                    'status' => false,
                    'message' => 'E-Book not found'
                ], 404);
            }
            $ebook->delete();
            return response()->json([
                'status' => true,
                'message' => 'E-Book deleted successfully'
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
