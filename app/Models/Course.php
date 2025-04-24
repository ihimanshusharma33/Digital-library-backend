<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'total_semesters',
        'department',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_semesters' => 'integer',
    ];

    // Relationships
    public function books()
    {
        return $this->hasMany(Book::class, 'course_code', 'course_code');
    }

    public function questionPapers()
    {
        return $this->hasMany(QuestionPaper::class, 'course_code', 'course_code');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'course_code', 'course_code');
    }
}