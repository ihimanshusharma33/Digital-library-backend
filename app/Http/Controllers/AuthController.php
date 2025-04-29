<?php

namespace App\Http\Controllers;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Factories\PayloadFactory;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Return response with token
            return response()->json([
                'status' => true,
                'code' => 201,
                'message' => 'User created successfully.',
                'data' => [
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Server error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            // Check if user exists
            $user = User::where('email', $credentials['email'])->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'User not found.'
                ], 404);
            }

            // Optional: Check if user is active
            if (isset($user->is_active) && !$user->is_active) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Your account is deactivated. Please contact support.'
                ], 403);
            }

            // Attempt login
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Invalid credentials.'
                ], 401);
            }

            // Success
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Login successful.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                    // add more user fields if needed
                ]
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Could not create token.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'An unexpected error occurred during login.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log out the user (invalidate the token)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Successfully logged out.'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Failed to logout.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
