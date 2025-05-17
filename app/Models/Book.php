<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Important: Specify the primary key
    protected $primaryKey = 'book_id';

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_year',
        'quantity',
        'available_quantity',
        'shelf_location',
        'category',
        'course_id',
        'is_available',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'quantity' => 'integer',
        'available_quantity' => 'integer',
        'is_available' => 'boolean',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function issuedBooks()
    {
        return $this->hasMany(IssuedBook::class, 'book_id', 'book_id');
    }
}