<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    protected $fillable = ['academic_year_id', 'grade', 'major', 'name'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classAssignment()
    {
        return $this->hasOne(CounselorClassAssignment::class, 'student_class_id');
    }

    public function counselor()
    {
        return $this->hasOneThrough(
            User::class,
            CounselorClassAssignment::class,
            'student_class_id',
            'id',
            'id',
            'counselor_id'
        );
    }
}
