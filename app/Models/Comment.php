<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'app_user_id',
        'post_id',
        'parent_id',
        'content',
        'status',
        'hate_speech_score',
        'is_ai_checked',
        'device_info'
    ];

    protected $casts = [
        'is_ai_checked' => 'boolean',
        'device_info' => 'array',
    ];

    // --- Relationships ---

    // 1. The Author
    public function author()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    // 2. The Post it belongs to
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // 3. Replies (Children)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // 4. Parent Comment (If this is a reply)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}