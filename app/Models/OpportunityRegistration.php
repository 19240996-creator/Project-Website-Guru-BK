<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportunityRegistration extends Model
{
    protected $fillable = [
        'opportunity_id',
        'student_id',
        'status',
        'registered_at',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
