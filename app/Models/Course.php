<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Important: Specify the primary key
    protected $primaryKey = 'course_id';

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

    // Define relationships
    public function users()
    {
        return $this->hasMany(User::class, 'course_id', 'course_id');
    }

    // Relationships
    public function books()
    {
        return $this->hasMany(Book::class, 'course_id', 'course_id');
    }

    public function questionPapers()
    {
        return $this->hasMany(QuestionPaper::class, 'course_cid', 'course_code');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'course_code', 'course_code');
    }
}