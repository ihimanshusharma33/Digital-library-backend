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
                    'notification_id', 'title', 'description', 'user_id', 'notification_type',
                    'attachment_url', 'created_at'
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
                    'course_id', 'semester', 'notification_type',
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
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,user_id',
            'notification_type' => 'nullable|string|max:255',
            'attachment_url' => 'nullable|string|max:255',
            'attachment_name' => 'nullable|string|max:255',
            'attachment_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'title',
            'description',
            'user_id',
            'notification_type',
            'attachment_url',
            'attachment_name',
            'attachment_type',
        ]);

        $notification = Notification::create($data);

        // Clear relevant caches
        \Cache::forget("notifications_simple_list");
        if ($notification->user_id) {
            \Cache::forget("user_{$notification->user_id}_notifications_page_1");
        } else {
            \Cache::flush();
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
        $notification = Notification::with('user')->find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
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
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,user_id',
            'notification_type' => 'sometimes|required|string|max:50',
            'attachment_url' => 'nullable|string|max:255',
            'attachment_name' => 'nullable|string|max:255',
            'attachment_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $notification->update($request->only([
            'title',
            'description',
            'user_id',
            'notification_type',
            'attachment_url',
            'attachment_name',
            'attachment_type'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Notification updated successfully',
            'data' => $notification
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
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        $notification->delete();
        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}