<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'avatar',
        'birth_place',
        'birth_date',
        'status',
        'attention_level',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'parent_job',
        'special_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function counselorAssignment()
    {
        return $this->hasOne(CounselorAssignment::class, 'student_id');
    }

    public function counselor()
    {
        return $this->hasOneThrough(
            User::class,
            CounselorAssignment::class,
            'student_id',
            'id',
            'id',
            'counselor_id'
        );
    }

    public function bkClassAttendances()
    {
        return $this->hasMany(BkClassAttendance::class, 'student_id');
    }

    public function bkClassSubmissions()
    {
        return $this->hasMany(BkClassTaskSubmission::class, 'student_id');
    }

    /**
     * Scope query untuk membatasi akses data siswa:
     * - koordinator_bk: dapat mengakses seluruh data siswa sekolah
     * - guru_bk: HANYA dapat mengakses siswa yang telah ditugaskan secara resmi
     * - siswa: hanya dapat mengakses data dirinya sendiri
     */
    public function scopeAccessibleBy($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if (in_array($user->role, ['koordinator_bk', 'kepala_kampus', 'admin_dico'])) {
            return $query;
        }

        if ($user->role === 'guru_bk') {
            return $query->whereHas('counselorAssignment', function ($q) use ($user) {
                $q->where('counselor_id', $user->id);
            });
        }

        if ($user->role === 'siswa') {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function achievements()
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function counselings()
    {
        return $this->hasMany(Counseling::class);
    }

    public function assessmentResults()
    {
        return $this->hasMany(StudentAssessmentResult::class);
    }

    public function futurePlan()
    {
        return $this->hasOne(StudentFuturePlan::class)->latestOfMany();
    }

    public function futurePlans()
    {
        return $this->hasMany(StudentFuturePlan::class);
    }

    public function opportunityRegistrations()
    {
        return $this->hasMany(OpportunityRegistration::class);
    }

    public function alumniTracking()
    {
        return $this->hasOne(AlumniTracking::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return \App\Services\AvatarService::getAvatarUrl($this->avatar, 'siswa');
    }

    public function hasCustomAvatar(): bool
    {
        return \App\Services\AvatarService::hasCustomAvatar($this->avatar);
    }

    public function getEncryptedIdAttribute(): string
    {
        return \App\Services\Security\SecurePath::encryptId($this->id);
    }
}
