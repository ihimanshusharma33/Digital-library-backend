<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications with a simple format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Create cache key for notifications
        $cacheKey = "notifications_simple_list";
        
        // Try to get data from cache first (5 minutes TTL)
        $notifications = Cache::remember($cacheKey, 300, function () {
            return Notification::orderBy('created_at', 'desc')
                ->select([
                    'id', 'title', 'description', 'user_id',
                    'course_code', 'semester', 'notification_type',
                    'attachment_url', 'is_read', 'read_at', 'expires_at', 'created_at'
                ])
                ->get();
        });
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    
    /**
     * Get notifications for the authenticated user with pagination and query optimization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserNotifications(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->query('per_page', 15);
        
        // Create cache key specific to this user
        $cacheKey = "user_{$user->id}_notifications_page_{$request->query('page', 1)}";
        
        // Try to get data from cache first (5 minutes TTL)
        $notifications = Cache::remember($cacheKey, 300, function () use ($user, $perPage) {
            return Notification::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereNull('user_id'); // Get general notifications too
                })
                ->select([
                    'id', 'title', 'description', 'user_id',
                    'course_code', 'semester', 'notification_type',
                    'attachment_url', 'is_read', 'read_at', 'expires_at', 'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Store a newly created notification in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'course_code' => 'nullable|string|max:50',
            'semester' => 'nullable|integer|min:1',
            'notification_type' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Fill data from request
        $data = $request->all();
        
        // Handle file upload if a file was provided
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            // Validate the file
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 400);
            }
            
            // Create a new multipart form request to the file upload service
            $response = Http::attach(
                'file', 
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://file-upload-eaky.onrender.com/upload');
            
            // Check if the upload was successful
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file to server',
                    'error' => $response->body()
                ], 500);
            }
            
            // Get the file URL from the response
            $responseData = $response->json();
            if (!isset($responseData['url'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from file server',
                    'error' => $responseData
                ], 500);
            }
            
            // Set the attachment URL in the data array
            $data['attachment_url'] = $responseData['url'];
        }

        $notification = Notification::create($data);

        // Clear relevant caches when creating a new notification
        Cache::forget("notifications_simple_list");
        if ($notification->user_id) {
            Cache::forget("user_{$notification->user_id}_notifications_page_1");
        } else {
            Cache::flush(); // Clear all cache for general notifications
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

    /**
     * Display the specified notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Try to get from cache first
        $notification = Cache::remember("notification_{$id}", 300, function () use ($id) {
            return Notification::select([
                'id', 'title', 'description', 'user_id', 
                'course_code', 'semester', 'notification_type',
                'attachment_url', 'is_read', 'read_at', 'expires_at', 'created_at'
            ])
            ->find($id);
        });
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Update the specified notification in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Use findOrFail for more efficient query
        $notification = Notification::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'course_code' => 'nullable|string|max:50',
            'semester' => 'nullable|integer|min:1',
            'notification_type' => 'nullable|string|max:255',
            'is_read' => 'nullable|boolean',
            'read_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Fill data from request
        $data = $request->except(['attachment_name', 'attachment_type']);
        
        // Handle file upload if a file was provided
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            // Validate the file
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 400);
            }
            
            // Create a new multipart form request to the file upload service
            $response = Http::attach(
                'file', 
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://file-upload-eaky.onrender.com/upload');
            
            // Check if the upload was successful
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file to server',
                    'error' => $response->body()
                ], 500);
            }
            
            // Get the file URL from the response
            $responseData = $response->json();
            if (!isset($responseData['url'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from file server',
                    'error' => $responseData
                ], 500);
            }
            
            // Set the attachment URL in the data array
            $data['attachment_url'] = $responseData['url'];
        }

        $notification->update($data);
        
        // Clear relevant caches after update
        Cache::forget("notification_{$id}");
        Cache::forget("notifications_simple_list");
        if ($notification->user_id) {
            Cache::forget("user_{$notification->user_id}_notifications_page_1");
        } else {
            Cache::forget("notifications_page_1_perpage_15");
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification updated successfully',
            'data' => $notification->only([
                'id', 'title', 'description', 'user_id',
                'course_code', 'semester', 'notification_type',
                'attachment_url', 'is_read', 'read_at', 'expires_at', 'created_at'
            ])
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        // Use findOrFail and direct DB update for efficiency
        $notification = Notification::findOrFail($id);

        // Update only necessary fields
        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();
        
        // Clear cache for this notification
        Cache::forget("notification_{$id}");
        if ($notification->user_id) {
            Cache::forget("user_{$notification->user_id}_notifications_page_1");
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Use mass update instead of individual updates - much faster
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
            
        // Clear all caches for this user
        Cache::forget("user_{$user->id}_notifications_page_1");

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'count' => $count
        ]);
    }

    /**
     * Remove the specified notification from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Use findOrFail for more efficient query with proper error handling
        $notification = Notification::findOrFail($id);
        
        // Store user_id before deleting for cache clearing
        $userId = $notification->user_id;
        
        $notification->delete();
        
        // Clear relevant caches
        Cache::forget("notification_{$id}");
        if ($userId) {
            Cache::forget("user_{$userId}_notifications_page_1");
        } else {
            Cache::forget("notifications_page_1_perpage_15");
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}