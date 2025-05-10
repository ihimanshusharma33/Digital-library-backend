<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\IssuedBook;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class userController extends Controller
{
    //
    public function getUser(Request $request)
    {
        try {
            //code...
            $users = User::all();
            return response()->json([
                'status' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function addUser(Request $request)
    {
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'nullable|string|min:8', // Made password optional 
                'phone_number' => 'nullable|string|max:15',
                'department' => 'nullable|string|max:100',
                'university_roll_number' => 'nullable|string|max:50|unique:users',
                'course_code' => 'nullable|string|exists:courses,course_code',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 = Validation Error
            }

            // Generate a unique 8-digit e-library ID for students
            $library_id = $this->generateUniqueELibraryId();
            $password = $request->password;
            $role = $request->role ?? 'student';

            if (!$password || $role === 'student') {
                $password = $library_id; // Set password to e-library ID
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($password),
                'role' => $role,
                'library_id' => $library_id,
                'phone_number' => $request->phone_number,
                'department' => $request->department,
                'university_roll_number' => $request->university_roll_number,
                'course_code' => $request->course_code,
                'email_verified_at' => $request->email_verified ? now() : null,
            ]);

            // Prepare response message
            $passwordMessage = '';
            if ($role === 'student') {
                $passwordMessage = 'Initial password has been set to the library card number. Student should reset it on first login.';
            }

            return response()->json([
                'status' => true,
                'message' => 'User created successfully. ' . $passwordMessage,
                'data' => $user
            ], 201); // 201 = Created
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            //code...
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'User retrieved successfully',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            //code...
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            $user->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function deleteUser($id)
    {
        try {
            //code...
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getLibraryCard($id)
    {
        try {
            //code...
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Library card retrieved successfully',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function issueBooks(Request $request)
    {
        try {
            //code...
            $user = User::find($request->id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'message' => 'Library card retrieved successfully',
                'data' => $user
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
     * Generate a unique 8-digit e-library ID
     * 
     * @return string
     */

    private function generateUniqueELibraryId()
    {
        $isUnique = false;
        $library_id = '';

        while (!$isUnique) {
            // Generate random 8-digit number
            $library_id = str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

            // Check if it's unique
            $exists = User::where('library_id', $library_id)->exists();

            if (!$exists) {
                $isUnique = true;
            }
        }

        return $library_id;
    }

    /**
     * Search users by various criteria
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = User::query();

            // Search by library ID
            if ($request->has('library_id')) {
                $query->where('library_id', $request->library_id);
            }

            // Search by name
            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            // Search by email
            if ($request->has('email')) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }

            // Additional filters as needed
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            $users = $query->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No users found matching the criteria'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'library_id' => $user->library_id,
                        'role' => $user->role,
                        // Add other fields as needed
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Issue a book to a user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueBook(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'user_id' => 'required|exists:users,id',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
                'issued_by' => 'required|exists:users,id',
                'remarks' => 'nullable|string'
            ]);

            // Check if book has available copies
            $book = Book::findOrFail($request->book_id);

            if ($book->available_quantity <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This book is currently out of stock'
                ], 400);
            }

            // Create new issued book record
            $issuedBook = IssuedBook::create([
                'book_id' => $request->book_id,
                'user_id' => $request->user_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'is_returned' => false,
                'remarks' => $request->remarks,
                'issued_by' => $request->issued_by
            ]);

            // Decrease available quantity
            $book->available_quantity = $book->available_quantity - 1;
            $book->save();

            // Load relationships for the response
            $issuedBook->load(['book', 'user']);

            return response()->json([
                'status' => true,
                'message' => 'Book issued successfully',
                'data' => $issuedBook,
                'book_status' => [
                    'available_quantity' => $book->available_quantity,
                    'total_quantity' => $book->total_quantity
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to issue book',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for a user by their library ID
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUserByLibraryId(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'library_id' => 'required|string',
            ]);

            // Search for user by library ID
            $user = User::where('library_id', $request->library_id)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found with the provided library ID'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'User found',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to search for user',
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
            $varOcg = 'getUserIssuedBooks';

            Log::info('[' . $varOcg . '] getUserIssuedBooks.', [
                'library_id' => $request->input('library_id'),
            ]);

            $request->validate([
                'library_id' => 'required|string',
            ]);

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
            $booksWithFines = $issuedBooks->map(function ($issuedBook) use ($today) {
                $fineAmount = $issuedBook->fine_amount ?? 0;
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
                    if ($issuedBook->return_date) {
                        $returnDate = \Carbon\Carbon::parse($issuedBook->return_date);
                        if ($returnDate->gt($dueDate)) {
                            $status = 'Returned Late';
                        }
                    }
                }

                return [
                    'id' => $issuedBook->id,
                    'book_id' => $issuedBook->book_id,
                    'book_title' => $issuedBook->book->title ?? 'Unknown Title',
                    'book_author' => $issuedBook->book->author ?? 'Unknown Author',
                    'book_isbn' => $issuedBook->book->isbn ?? 'No ISBN',
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
                    'status' => false,
                    'message' => 'No books found matching the criteria'
                ], 404);
            }

            // Check availability for each book
            $booksWithAvailability = $books->map(function ($book) {
                // A book is available if available_quantity > 0
                $isAvailable = $book->available_quantity > 0;

                $data = [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'is_available' => $isAvailable,
                    'available_quantity' => $book->available_quantity,
                    'total_quantity' => $book->total_quantity
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
                'status' => true,
                'data' => $booksWithAvailability,
                'message' => $booksWithAvailability->contains('is_available', true)
                    ? 'Books available for checkout'
                    : 'Sorry, no books are currently available for checkout'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
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

            // Increase available quantity in the book record
            $book = Book::find($issuedBook->book_id);
            if ($book) {
                $book->available_quantity = $book->available_quantity + 1;
                $book->save();
            }

            // Reload the relationships for response
            $issuedBook->load(['book', 'user']);

            return response()->json([
                'status' => true,
                'message' => $fineAmount > 0
                    ? "Book returned successfully. Fine amount: ₹{$fineAmount}"
                    : "Book returned successfully",
                'data' => $issuedBook,
                'book_status' => $book ? [
                    'available_quantity' => $book->available_quantity,
                    'total_quantity' => $book->total_quantity
                ] : null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to process book return',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserIssuedBooksById($id){
        
    try {
        $varOcg = 'getUserIssuedBooksById';
        Log::info("[$varOcg] Fetching issued books for user ID: $id");

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found with the provided ID'
            ], 404);
        }

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

        $today = \Carbon\Carbon::now()->startOfDay();

        $booksWithFines = $issuedBooks->map(function ($issuedBook) use ($today) {
            $fineAmount = $issuedBook->fine_amount ?? 0;
            $dueDate = \Carbon\Carbon::parse($issuedBook->due_date);
            $status = 'On Time';

            if (!$issuedBook->is_returned) {
                if ($today->gt($dueDate)) {
                    $daysLate = $today->diffInDays($dueDate);
                    $fineAmount = $daysLate * 10;
                    $status = 'Overdue';
                } else {
                    $status = 'Issued';
                }
            } else {
                $status = 'Returned';
                if ($issuedBook->return_date) {
                    $returnDate = \Carbon\Carbon::parse($issuedBook->return_date);
                    if ($returnDate->gt($dueDate)) {
                        $status = 'Returned Late';
                    }
                }
            }

            return [
                'id' => $issuedBook->id,
                'book_id' => $issuedBook->book_id,
                'book_title' => $issuedBook->book->title ?? 'Unknown Title',
                'book_author' => $issuedBook->book->author ?? 'Unknown Author',
                'book_isbn' => $issuedBook->book->isbn ?? 'No ISBN',
                'issue_date' => $issuedBook->issue_date,
                'due_date' => $issuedBook->due_date,
                'return_date' => $issuedBook->return_date,
                'is_returned' => $issuedBook->is_returned,
                'fine_amount' => $fineAmount,
                'status' => $status,
                'remarks' => $issuedBook->remarks,
            ];
        });

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

}
