<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTranslation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'translatable_id',
        'translatable_type',
        'locale',
        'content'
    ];

    // --- Relationships ---

    // Get the parent item (Post or Comment)
    public function translatable()
    {
        return $this->morphTo();
    }
}