<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BkClassAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'bk_class_id',
        'student_id',
        'status',
        'attended_at',
        'ip_address',
        'user_agent',
        'is_manual_correction',
        'correction_reason',
        'corrected_by',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'is_manual_correction' => 'boolean',
    ];

    public function bkClass()
    {
        return $this->belongsTo(BkClass::class, 'bk_class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function corrector()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpa / Tidak Hadir',
            default => 'Hadir',
        };
    }
}
