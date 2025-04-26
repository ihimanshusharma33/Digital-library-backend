<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'file_path',
        'course_code',
        'semester',
        'is_verified',
    ];

    protected $casts = [
        'semester' => 'integer',
        'is_verified' => 'boolean',
    ];

    // Relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}
