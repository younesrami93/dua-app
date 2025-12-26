<?php

namespace App\Models;

// 1. CHANGE THIS: Extend 'Authenticatable' instead of 'Model'
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AppUser extends Authenticatable
{
    use HasApiTokens, Notifiable,SoftDeletes;

    // 2. Allow mass assignment for these fields
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar_url',
        'auth_provider',
        'social_id',
        'is_guest',
        'status',
        'hate_speech_violation_count',
        'banned_posts_count',
        'device_uuid',
        'last_device_info',
        'country_code',
        'last_ip_address'
    ];

    // 3. Hide these when returning user data to the app
    protected $hidden = [
        'password',
        'social_id', // Keep this private
    ];

    // 4. Auto-convert types
    protected $casts = [
        'is_guest' => 'boolean',
        'last_device_info' => 'array', // JSON becomes a PHP Array automatically
        'password' => 'hashed',
    ];

    // 5. Relationships
    public function posts()
    {
        return $this->hasMany(Post::class, 'app_user_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'app_user_id');
    }
    
}