<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Clear the notifications table first
        DB::table('notifications')->truncate();

        // Get existing user IDs
        $userIds = User::pluck('user_id')->toArray();
        
        // If there are no users, you can't create notifications with user_id
        if (empty($userIds)) {
            $this->command->info('No users found. Please run the UserSeeder first.');
            return;
        }

        // Get a random user ID
        $randomUserId = $userIds[array_rand($userIds)];

        // Create dummy notifications
        $notifications = [
            [
                'title' => 'Assignment Deadline Reminder',
                'description' => 'Please submit your assignment by tomorrow.',
                'user_id' => $randomUserId, // Use an actual existing user ID
                'notification_type' => 'due_date',
                'attachment_url' => 'https://example.com/assignment1.pdf',
                'attachment_name' => 'Assignment 1',
                'attachment_type' => 'pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Overdue Notice: Library Book',
                'description' => 'You have an overdue book. Please return it ASAP.',
                'user_id' => $randomUserId, // Use an actual existing user ID
                'notification_type' => 'overdue',
                'attachment_url' => null,
                'attachment_name' => null,
                'attachment_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'General Notification',
                'description' => 'Semester break will begin from next Monday.',
                'user_id' => $randomUserId, // Use an actual existing user ID
                'notification_type' => 'general',
                'attachment_url' => null,
                'attachment_name' => null,
                'attachment_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert notifications
        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
        
        $this->command->info('Created ' . count($notifications) . ' notifications.');
    }
}