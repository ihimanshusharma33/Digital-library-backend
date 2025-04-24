<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NotesController extends Controller
{
    //
    public function getNotes(Request $request)
    {
        try {
            //code...
            $notes = Note::all();
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
          $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'subject' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'file_path' => 'required|string|max:255',
                'course_code' => 'required|string|max:50',
                'semester' => 'required|integer',
                'is_verified' => 'boolean',
            ]);
            
            $note = Note::create($validatedData);

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
            //code...
            $note = Note::find($id);
            if (!$note) {
                return response()->json([
                    'status' => false,
                    'message' => 'Note not found'
                ], 404);
            }
            $note->update($request->all());
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
            $note->delete();
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
}
