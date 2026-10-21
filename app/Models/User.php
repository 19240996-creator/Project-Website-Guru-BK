<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function counselingsHandled()
    {
        return $this->hasMany(Counseling::class, 'counselor_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function studentAssignments()
    {
        return $this->hasMany(CounselorAssignment::class, 'counselor_id');
    }

    public function bkClasses()
    {
        return $this->hasMany(BkClass::class, 'counselor_id');
    }

    public function classAssignments()
    {
        return $this->hasMany(CounselorClassAssignment::class, 'counselor_id');
    }

    public function assignedStudents()
    {
        return $this->hasManyThrough(
            Student::class,
            CounselorAssignment::class,
            'counselor_id',
            'id',
            'id',
            'student_id'
        );
    }

    public function isKoordinatorBk(): bool
    {
        return $this->role === 'koordinator_bk';
    }

    public function isGuruBk(): bool
    {
        return $this->role === 'guru_bk';
    }

    public function isGuruOrKoordinator(): bool
    {
        return in_array($this->role, ['guru_bk', 'koordinator_bk']);
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function canAccessStudent($studentId): bool
    {
        if ($this->isKoordinatorBk()) {
            return true;
        }

        if ($this->isGuruBk()) {
            return $this->studentAssignments()->where('student_id', $studentId)->exists();
        }

        if ($this->isSiswa()) {
            return $this->student && $this->student->id == $studentId;
        }

        return false;
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'koordinator_bk' => 'Guru Koordinator BK',
            'guru_bk' => 'Guru BK',
            'siswa' => 'Siswa',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        return \App\Services\AvatarService::getAvatarUrl($this->avatar, $this->role ?? 'siswa');
    }

    public function hasCustomAvatar(): bool
    {
        return \App\Services\AvatarService::hasCustomAvatar($this->avatar);
    }
}
