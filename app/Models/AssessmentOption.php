<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentOption extends Model
{
    protected $fillable = ['question_id', 'option_text', 'score_value', 'dimension_code'];

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}
