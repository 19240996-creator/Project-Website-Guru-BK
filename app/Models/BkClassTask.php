<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BkClassTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'bk_class_id',
        'title',
        'instructions',
        'attachment_path',
        'attachment_name',
        'start_time',
        'due_time',
        'allowed_file_types',
        'max_file_size_kb',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'due_time' => 'datetime',
    ];

    public function bkClass()
    {
        return $this->belongsTo(BkClass::class, 'bk_class_id');
    }

    public function submissions()
    {
        return $this->hasMany(BkClassTaskSubmission::class, 'bk_class_task_id');
    }

    public function isExpired(): bool
    {
        return Carbon::now('Asia/Jakarta')->gt($this->due_time);
    }

    public function isOpen(): bool
    {
        return Carbon::now('Asia/Jakarta')->gte($this->start_time);
    }

    public function submissionFor(?Student $student): ?BkClassTaskSubmission
    {
        if (!$student) {
            return null;
        }

        return $this->submissions()->where('student_id', $student->id)->first();
    }
}
