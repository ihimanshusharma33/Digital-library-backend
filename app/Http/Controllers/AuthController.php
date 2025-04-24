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

            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Invalid credentials.'
                ], 401);
            }
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Login successful.',
                'token' => $token
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false, 
                'code' => 500,
                'message' => 'Could not create token.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
