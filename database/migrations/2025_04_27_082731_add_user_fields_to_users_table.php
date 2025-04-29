<?php

namespace App\Http\Controllers;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\IssuedBook;

class AddUserFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('department')->nullable();
            $table->string('university_roll_number')->nullable()->unique();
            $table->string('course_code')->nullable();
            
            // Add foreign key constraint
            $table->foreign('course_code')
                  ->references('course_code')
                  ->on('courses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['course_code']);
            
            // Drop columns
            $table->dropColumn(['phone_number', 'department', 'university_roll_number', 'course_code']);
        });
    }
}

class userController extends Controller
{
    public function getUser(Request $request)
    {
        try {
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
                ], 422); 
                // 422 = Validation Error
            }                  
            // Generate a unique 8-digit e-library ID for students    
            $e_library_id = $this->generateUniqueELibraryId();                    
            $password = $request->password;     
            $role = $request->role ?? 'student';             
            if (!$password || $role === 'student') {        
                $password = $e_library_id; // Set password to e-library ID
            }                    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($password),
                'role' => $role,
                'e_library_id' => $e_library_id,
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

    public function returnBooks(Request $request)
    {
        try {
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
        $e_library_id = '';
        
        while (!$isUnique) {
            // Generate random 8-digit number
            $e_library_id = str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            // Check if it's unique
            $exists = User::where('e_library_id', $e_library_id)->exists();
            
            if (!$exists) {
                $isUnique = true;
            }
        }
        
        return $e_library_id;
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
                'data' => $users->map(function($user) {
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
            // Check if the book is already issued and not returned
            $alreadyIssued = IssuedBook::where('book_id', $request->book_id)
                ->where('is_returned', false)
                ->exists();
            if ($alreadyIssued) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This book is already issued to another user'
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
            // Load relationships for the response
            $issuedBook->load(['book', 'user']);
            return response()->json([
                'status' => 'success',
                'message' => 'Book issued successfully',
                'data' => $issuedBook
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to issue book',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
