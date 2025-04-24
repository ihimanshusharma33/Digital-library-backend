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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->string('shelf_location')->nullable();
            $table->string('category')->nullable();
            
            // Course-related fields
            $table->string('course_code')->nullable();
            $table->foreign('course_code')->references('course_code')->on('courses')->onDelete('set null');
            $table->integer('semester')->nullable();
            
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
