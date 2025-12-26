<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'app_user_id',
        'category_id',
        'content',
        'is_anonymous',
        'status',
        'is_ai_checked',
        'hate_speech_score',
        'safety_label',
        'device_info'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_ai_checked' => 'boolean',
        'device_info' => 'array',
        'banned_at' => 'datetime',
    ];

    // --- Relationships ---

    public function author()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // ✅ NEW: Required for "Like" check
    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    // ✅ NEW: Useful for fetching/deleting comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}