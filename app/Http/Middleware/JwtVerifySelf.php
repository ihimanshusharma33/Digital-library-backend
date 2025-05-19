<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtVerifySelf
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            
            
            // Check if user is requesting their own data or if they're staff
            $requestedId = $request->route('user_id');
            
            if ($user->user_id != $requestedId && !in_array($user->role, ['admin', 'librarian'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. You can only access your own information.'
                ], 403);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication error',
                'error' => $e->getMessage()
            ], 401);
        }
        
        return $next($request);
    }
}