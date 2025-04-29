<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Rename message column to description to match frontend naming
            $table->renameColumn('message', 'description');
            // Add new columns for course information
            $table->string('course_code')->nullable()->after('user_id');
            $table->integer('semester')->nullable()->after('course_code');
            
            // Add new columns for attachment information
            $table->string('attachment_url')->nullable()->after('notification_type');
            $table->string('attachment_name')->nullable()->after('attachment_url');
            $table->string('attachment_type')->default('pdf')->nullable()->after('attachment_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Rename description back to message
            $table->renameColumn('description', 'message');
            
            // Drop the new columns
            $table->dropColumn([
                'course_code',
                'semester',
                'attachment_url',
                'attachment_name',
                'attachment_type'
            ]);
        });
    }
};
