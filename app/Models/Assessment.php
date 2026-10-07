<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['title', 'category', 'description', 'instructions', 'is_active'];

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('sort_order');
    }

    public function studentResults()
    {
        return $this->hasMany(StudentAssessmentResult::class);
    }
}
