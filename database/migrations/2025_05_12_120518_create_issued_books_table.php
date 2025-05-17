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
        Schema::create('issued_books', function (Blueprint $table) {
            $table->id('issue_id');
            $table->unsignedBigInteger('book_id')->nullable();;
            $table->foreign('book_id')->references('book_id')->on('books')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable(); // Student ID who has issued the book
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->boolean('is_returned')->default(false);
            $table->text('remarks')->nullable();
            $table->string('issued_by')->nullable(); // Librarian or staff who issued the book
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issued_books_');
    }
};
