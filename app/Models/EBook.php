<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EBook extends Model
{
    use HasFactory;

    protected $table = 'ebooks';
    protected $primaryKey = 'ebook_id';
    protected $fillable = [
        'title',
        'description',
        'author',
        'file_path',
        'course_id',
        'semester',
        'subject',
    ];

    protected $casts = [
        'semester' => 'integer',
    ];

    // Relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
