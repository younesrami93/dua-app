<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentReport extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'reporter_id',
        'reported_id',
        'reported_type',
        'reason',
        'details',
        'status',
        'admin_notes'
    ];

    // --- Relationships ---

    // 1. The User who submitted the report
    public function reporter()
    {
        return $this->belongsTo(AppUser::class, 'reporter_id');
    }

    // 2. The Content being reported (Post or Comment)
    public function reported()
    {
        return $this->morphTo();
    }
}