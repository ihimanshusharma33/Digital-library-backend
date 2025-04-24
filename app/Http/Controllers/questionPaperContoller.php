<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionPaper;
class questionPaperContoller extends Controller
{
    //
    public function getQuestionPaper(Request $request)
    {
        try {
            //code...
            $questionPapers = QuestionPaper::all();
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
    public function addQuestionPaper(Request $request)
    {
        try {
            //code...
            $questionPaper = new QuestionPaper();
            $questionPaper->title = $request->title;
            $questionPaper->content = $request->content;
            $questionPaper->save();

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
            //code...
            $questionPaper = QuestionPaper::find($id);
            if (!$questionPaper) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question paper not found'
                ], 404);
            }
            $questionPaper->update($request->all());
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
            $questionPaper->delete();
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
            //code...
            $questionPaper = QuestionPaper::find($id);
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
}
