<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interaction extends Model
{
    protected $fillable = [
        'app_user_id',
        'post_id',
        'type'
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}