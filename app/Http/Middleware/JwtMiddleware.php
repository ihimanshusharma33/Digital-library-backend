<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if token exists
            if (!$token = JWTAuth::getToken()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Authorization token not found',
                    'error' => 'token_absent'
                ], 401);
            }

            // Try to parse and authenticate token
            $user = JWTAuth::authenticate($token);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                    'error' => 'user_not_found'
                ], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token has expired',
                'error' => 'token_expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token',
                'error' => 'token_invalid'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token could not be parsed or is absent',
                'error' => 'token_error'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization error',
                'error' => $e->getMessage()
            ], 500);
        }

        // Add user to request for controller access
        $request->auth = $user;

        return $next($request);
    }

}
