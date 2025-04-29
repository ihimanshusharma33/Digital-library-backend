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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing course_name column if it exists
            if (Schema::hasColumn('users', 'course_name')) {
                $table->dropColumn('course_name');
            }
            
            // Add course_code column with foreign key constraint
            $table->string('course_code')->nullable();
            $table->foreign('course_code')
                  ->references('course_code')
                  ->on('courses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['course_code']);
            
            // Drop the course_code column
            $table->dropColumn('course_code');
            
            // Add back the course_name column
            $table->string('course_name')->nullable();
        });
    }
};
