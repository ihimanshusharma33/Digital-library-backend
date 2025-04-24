<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject',
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

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}