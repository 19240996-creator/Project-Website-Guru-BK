<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFuturePlan extends Model
{
    protected $fillable = [
        'student_id',
        'primary_goal',
        'college_target',
        'study_program',
        'entry_path',
        'work_target_field',
        'work_target_company',
        'business_field',
        'business_idea',
        'notes',
        'version',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
