<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniTracking extends Model
{
    protected $fillable = [
        'code',
        'student_id',
        'graduation_year',
        'tracking_period',
        'current_status',
        'institution_or_company',
        'major_or_position',
        'monthly_income_range',
        'notes',
        'allow_public_showcase',
    ];

    protected $casts = [
        'allow_public_showcase' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
