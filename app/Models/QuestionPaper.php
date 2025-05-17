<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    use HasFactory;

    
 protected $table = 'question_papers';
    protected $primaryKey = 'paper_id';
    protected $fillable = [
        'title',
        'subject',
        'year',
        'exam_type',
        'file_path',
        'course_id',
        'semester',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'semester' => 'integer',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}