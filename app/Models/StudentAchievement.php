<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    protected $fillable = ['student_id', 'title', 'level', 'year', 'description'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
