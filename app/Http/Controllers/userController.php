<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class userController extends Controller
{
    //
    public function getUser(Request $request){


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

    public function addUser(Request $request){

        try {
            //code...
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 = Validation Error
            }
            
            // Generate a unique 8-digit e-library ID for students
            $e_library_id = $this->generateUniqueELibraryId();
            
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role ?? 'student',
                'e_library_id' => $e_library_id,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 201); // 201 = Created   
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function getUserById($id){
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
    
    public function updateUser(Request $request, $id){
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
    public function deleteUser($id){
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

    public function getLibraryCard($id){
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

    public function issueBooks(Request $request){
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
    public function returnBooks(Request $request){
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
}
