<?php

namespace App\Http\Controllers;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Factories\PayloadFactory;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtp;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'identifier' => 'required|string', // Can be email, library_id, or phone_number
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

            $identifier = $request->identifier;
            
            // Find user by any of the three identifiers
            $user = User::where('email', $identifier)
                        ->orWhere('library_id', $identifier)
                        ->orWhere('phone_number', $identifier)
                        ->first();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'User not found.'
                ], 404);
            }

            // Check if user is active
            if (isset($user->is_active) && !$user->is_active) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Your account is deactivated. Please contact support.'
                ], 403);
            }

            // Attempt login
            if (!$token = JWTAuth::attempt([
                'email' => $user->email,
                'password' => $request->password
            ])) {
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
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'library_id' => $user->library_id,
                    'phone_number' => $user->phone_number
                ]
            ], 200);
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

    /**
     * Generate 6 digit OTP
     */
    private function generateOTP()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Request a password reset OTP
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => 422,
                    'message' => 'Email validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->email;
            $user = User::where('email', $email)->first();
            if(!$user) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'Email not found. Please  register first.'
                ], 404);
            }

            // Delete any existing OTPs for this email
            PasswordReset::where('email', '=', $email)->delete();

            // Generate new OTP
            $otp = $this->generateOTP();
            
            // Store OTP in the database with expiration time
            PasswordReset::create([
                'email' => $email,
                'otp' => bcrypt($otp), // Store hashed OTP for security
                'created_at' => now(),
                'expires_at' => now()->addMinutes(15) // OTP valid for 15 minutes
            ]);

            // Send OTP via email
            Mail::to($email)->send(new ResetPasswordOtp($otp, $user->name));

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Password reset OTP has been sent to your email.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Failed to process password reset request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP and reset password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string|size:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code' => 422,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->email;
            $otp = $request->otp;
            
            // Get the most recent OTP for this email
            $passwordReset = PasswordReset::where('email', $email)
                                         ->orderBy('created_at', 'desc')
                                         ->first();
            
            // Check if OTP exists and is not expired
            if (!$passwordReset) {
                return response()->json([
                    'status' => false,
                    'code' => 404,
                    'message' => 'No active password reset request found.'
                ], 404);
            }
            
            // Check if OTP is expired
            if (now()->gt($passwordReset->expires_at)) {
                // Delete expired OTP
                $passwordReset->delete();
                
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 401);
            }
            
            // Verify OTP
            if (!Hash::check($otp, $passwordReset->otp)) {
                return response()->json([
                    'status' => false,
                    'code' => 401,
                    'message' => 'Invalid OTP.'
                ], 401);
            }
            
            // Update user's password
            $user = User::where('email', $email)->first();
            $user->password = bcrypt($request->password);
            $user->save();
            
            // Delete used OTP
            $passwordReset->delete();
            
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Password has been reset successfully.'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'Failed to reset password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
