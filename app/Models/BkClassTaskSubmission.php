<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkClassTaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'bk_class_task_id',
        'student_id',
        'submission_text',
        'file_path',
        'file_name',
        'file_size',
        'submitted_at',
        'status',
        'score',
        'counselor_feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(BkClassTask::class, 'bk_class_task_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'diserahkan' => 'Diserahkan',
            'terlambat' => 'Terlambat Diserahkan',
            'dinilai' => 'Telah Dinilai',
            'perlu_revisi' => 'Perlu Revisi',
            default => 'Diserahkan',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 1) . ' MB';
        }

        return number_format($this->file_size / 1024, 0) . ' KB';
    }
}
