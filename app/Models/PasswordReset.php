<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    
    protected $table = 'password_reset_otps';
    
    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'otp',
        'created_at',
        'expires_at'
    ];
}
