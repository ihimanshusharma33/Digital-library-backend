<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class booksContoller extends Controller
{
    //
    public function getBooks(Request $request)
    {
        try {
            $books = Book::all();
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
            //code...
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|max:13|unique:books,isbn',
                'published_at' => 'required|date',
            ]);

            $book = new Book();
            $book->title = $request->title;
            $book->author = $request->author;
            $book->isbn = $request->isbn;
            $book->save();
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
            //code...
            $book = Book::find($id);
            if (!$book) {
                return response()->json([
                    'status' => false,
                    'message' => 'Book not found'
                ], 404);
            }
            $book->update($request->all());
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
            $book->delete();
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
}
