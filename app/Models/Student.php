<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'avatar',
        'birth_place',
        'birth_date',
        'status',
        'attention_level',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'parent_job',
        'special_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function achievements()
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function counselings()
    {
        return $this->hasMany(Counseling::class);
    }

    public function assessmentResults()
    {
        return $this->hasMany(StudentAssessmentResult::class);
    }

    public function futurePlan()
    {
        return $this->hasOne(StudentFuturePlan::class)->latestOfMany();
    }

    public function futurePlans()
    {
        return $this->hasMany(StudentFuturePlan::class);
    }

    public function opportunityRegistrations()
    {
        return $this->hasMany(OpportunityRegistration::class);
    }

    public function alumniTracking()
    {
        return $this->hasOne(AlumniTracking::class);
    }
}
