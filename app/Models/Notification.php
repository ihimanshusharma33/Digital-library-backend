<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'course_id',
        'semester',
        'notification_type',
        'attachment_url',
        'attachment_name',
        'attachment_type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'semester' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}