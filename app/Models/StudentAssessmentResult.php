<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessmentResult extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_id',
        'total_score',
        'dimension_scores',
        'result_category',
        'summary',
        'recommendations',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'dimension_scores' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
