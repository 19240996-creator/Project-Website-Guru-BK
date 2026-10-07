<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingFollowUp extends Model
{
    protected $fillable = [
        'counseling_id',
        'action_description',
        'target_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    public function counseling()
    {
        return $this->belongsTo(Counseling::class);
    }
}
