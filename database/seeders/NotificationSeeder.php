<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the notifications table first
        DB::table('notifications')->truncate();

        // Get some user IDs to associate with notifications
        $userIds = User::pluck('id')->toArray();
        
        // If there are no users, we'll create notifications with null user_id
        if (empty($userIds)) {
            $userIds = [null];
        }

        // Create dummy notifications
        $notifications = [
            [
                'title' => 'Welcome to E-Library!',
                'message' => 'Welcome to our E-Library system. Browse through our collection of books, notes, and question papers.',
                'user_id' => null, // null means it's for all users
                'notification_type' => 'general',
                'is_read' => false,
                'read_at' => null,
                'expires_at' => now()->addMonths(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'New Books Available',
                'message' => 'We have added new books to our collection. Check them out!',
                'user_id' => null,
                'notification_type' => 'general',
                'is_read' => false,
                'read_at' => null,
                'expires_at' => now()->addDays(30),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'System Maintenance',
                'message' => 'The system will be under maintenance on Sunday from 2 AM to 4 AM.',
                'user_id' => null,
                'notification_type' => 'system',
                'is_read' => false,
                'read_at' => null,
                'expires_at' => now()->addDays(7),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        // Add some user-specific notifications
        foreach ($userIds as $key => $userId) {
            if ($userId !== null) {
                $notifications[] = [
                    'title' => 'Book Due Reminder',
                    'message' => 'You have a book due for return in 3 days.',
                    'user_id' => $userId,
                    'notification_type' => 'due_date',
                    'is_read' => false,
                    'read_at' => null,
                    'expires_at' => now()->addDays(3),
                    'created_at' => now()->subDays(4),
                    'updated_at' => now()->subDays(4),
                ];

                $notifications[] = [
                    'title' => 'Overdue Book Notice',
                    'message' => 'One of your borrowed books is overdue. Please return it as soon as possible to avoid penalties.',
                    'user_id' => $userId,
                    'notification_type' => 'overdue',
                    'is_read' => rand(0, 1) ? true : false,
                    'read_at' => rand(0, 1) ? now()->subDays(rand(1, 3)) : null,
                    'expires_at' => now()->addDays(7),
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(rand(1, 3)),
                ];

                $notifications[] = [
                    'title' => 'New Course Materials Available',
                    'message' => 'New study materials for your enrolled courses are now available.',
                    'user_id' => $userId,
                    'notification_type' => 'course',
                    'is_read' => rand(0, 1) ? true : false,
                    'read_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
                    'expires_at' => now()->addDays(14),
                    'created_at' => now()->subDays(7),
                    'updated_at' => now()->subDays(7),
                ];
            }
        }

        // Insert notifications
        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
    }
}