<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counseling extends Model
{
    protected $fillable = [
        'code',
        'student_id',
        'counselor_id',
        'category_id',
        'topic',
        'story',
        'urgency',
        'preferred_schedule',
        'status',
        'scheduled_date',
        'scheduled_time',
        'scheduled_location',
        'counselor_notes',
        'confidential_level',
        'student_action_plan',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function category()
    {
        return $this->belongsTo(CounselingCategory::class, 'category_id');
    }

    public function followUps()
    {
        return $this->hasMany(CounselingFollowUp::class);
    }
}
