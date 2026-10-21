<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BkClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'counselor_id',
        'title',
        'description',
        'target_grade',
        'target_major',
        'target_class_ids',
        'class_date',
        'start_time',
        'end_time',
        'attendance_start_time',
        'attendance_end_time',
        'status',
    ];

    protected $casts = [
        'target_class_ids' => 'array',
        'class_date' => 'date',
    ];

    // ==========================================
    // RELATIONS
    // ==========================================

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function materials()
    {
        return $this->hasMany(BkClassMaterial::class, 'bk_class_id')->orderBy('order_num')->orderBy('created_at');
    }

    public function tasks()
    {
        return $this->hasMany(BkClassTask::class, 'bk_class_id')->orderBy('due_time');
    }

    public function attendances()
    {
        return $this->hasMany(BkClassAttendance::class, 'bk_class_id');
    }

    // ==========================================
    // ACCESSORS (SERVER TIME: WIB / Asia/Jakarta)
    // ==========================================

    public function getStartDateTimeAttribute(): Carbon
    {
        $dateStr = $this->class_date ? $this->class_date->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        return Carbon::parse($dateStr . ' ' . $this->start_time, 'Asia/Jakarta');
    }

    public function getEndDateTimeAttribute(): Carbon
    {
        $dateStr = $this->class_date ? $this->class_date->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        return Carbon::parse($dateStr . ' ' . $this->end_time, 'Asia/Jakarta');
    }

    public function getAttendanceStartDateTimeAttribute(): Carbon
    {
        $dateStr = $this->class_date ? $this->class_date->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        return Carbon::parse($dateStr . ' ' . $this->attendance_start_time, 'Asia/Jakarta');
    }

    public function getAttendanceEndDateTimeAttribute(): Carbon
    {
        $dateStr = $this->class_date ? $this->class_date->format('Y-m-d') : Carbon::now('Asia/Jakarta')->format('Y-m-d');
        return Carbon::parse($dateStr . ' ' . $this->attendance_end_time, 'Asia/Jakarta');
    }

    /**
     * Hitung status kelas secara otomatis berdasarkan jam server WIB:
     * - 'terjadwal': Waktu kelas belum mulai
     * - 'berlangsung': Jam pembelajaran sedang berlangsung
     * - 'selesai': Jam pembelajaran telah lewat
     * - 'dibatalkan': Jika diset manual sebagai batal
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'dibatalkan') {
            return 'dibatalkan';
        }

        $now = Carbon::now('Asia/Jakarta');

        if ($now->lt($this->start_date_time)) {
            return 'terjadwal';
        }

        if ($now->lte($this->end_date_time)) {
            return 'berlangsung';
        }

        return 'selesai';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->computed_status) {
            'terjadwal' => 'Terjadwal',
            'berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => 'Terjadwal',
        };
    }

    /**
     * Status jendela presensi berdasarkan jam server WIB:
     * - 'belum_dibuka': Sebelum attendance_start_time
     * - 'dibuka': Berada di rentang attendance_start_time s/d attendance_end_time
     * - 'ditutup': Sudah melewati attendance_end_time
     */
    public function getAttendanceStatusAttribute(): string
    {
        $now = Carbon::now('Asia/Jakarta');

        if ($now->lt($this->attendance_start_date_time)) {
            return 'belum_dibuka';
        }

        if ($now->lte($this->attendance_end_date_time)) {
            return 'dibuka';
        }

        return 'ditutup';
    }

    public function getAttendanceStatusLabelAttribute(): string
    {
        return match($this->attendance_status) {
            'belum_dibuka' => 'Presensi Belum Dibuka',
            'dibuka' => 'Presensi Sedang Dibuka',
            'ditutup' => 'Presensi Telah Ditutup',
            default => 'Presensi Ditutup',
        };
    }

    public function isAttendanceOpen(): bool
    {
        return $this->attendance_status === 'dibuka';
    }

    public function isOngoing(): bool
    {
        return $this->computed_status === 'berlangsung';
    }

    // ==========================================
    // TARGET STUDENTS & ACCESS SCOPE
    // ==========================================

    /**
     * Mengembalikan Query Builder daftar siswa target kelas BK ini:
     * Menyesuaikan target kelas / rombel, tingkat, jurusan, dan batasan penugasan Guru BK.
     */
    public function getTargetStudentsQuery()
    {
        $query = Student::with(['studentClass', 'counselorAssignment'])->where('status', 'aktif');

        if (!empty($this->target_class_ids) && is_array($this->target_class_ids)) {
            $query->whereIn('student_class_id', $this->target_class_ids);
        } else {
            if ($this->target_grade) {
                $query->whereHas('studentClass', fn($q) => $q->where('grade', $this->target_grade));
            }
            if ($this->target_major) {
                $query->whereHas('studentClass', fn($q) => $q->where('major', $this->target_major));
            }
        }

        // Jika dibuat oleh guru_bk (bukan koordinator), hanya siswa dalam penugasan resmi guru tersebut
        if ($this->counselor && $this->counselor->role === 'guru_bk') {
            $counselorId = $this->counselor_id;
            $query->whereHas('counselorAssignment', function ($q) use ($counselorId) {
                $q->where('counselor_id', $counselorId);
            });
        }

        return $query->orderBy('name');
    }

    public function getTargetStudents()
    {
        return $this->getTargetStudentsQuery()->get();
    }

    /**
     * Memeriksa apakah seorang siswa berhak mengakses kelas ini.
     */
    public function isStudentEligible(?Student $student): bool
    {
        if (!$student || $student->status !== 'aktif') {
            return false;
        }

        // Cek filter rombel/kelas
        if (!empty($this->target_class_ids) && is_array($this->target_class_ids)) {
            if (!in_array($student->student_class_id, $this->target_class_ids)) {
                return false;
            }
        } else {
            if ($this->target_grade && optional($student->studentClass)->grade !== $this->target_grade) {
                return false;
            }
            if ($this->target_major && optional($student->studentClass)->major !== $this->target_major) {
                return false;
            }
        }

        // Cek penugasan konselor jika pembuat kelas adalah guru_bk biasa
        if ($this->counselor && $this->counselor->role === 'guru_bk') {
            $hasAssignment = CounselorAssignment::where('student_id', $student->id)
                ->where('counselor_id', $this->counselor_id)
                ->exists();
            if (!$hasAssignment) {
                return false;
            }
        }

        return true;
    }

    /**
     * Label teks kelas sasaran untuk tampilan UI
     */
    public function getTargetClassesTextAttribute(): string
    {
        if (!empty($this->target_class_ids) && is_array($this->target_class_ids)) {
            $classes = StudentClass::whereIn('id', $this->target_class_ids)->pluck('name')->toArray();
            return !empty($classes) ? implode(', ', $classes) : 'Kelas Sasaran Khusus';
        }

        $parts = [];
        if ($this->target_grade) {
            $parts[] = 'Tingkat ' . $this->target_grade;
        }
        if ($this->target_major) {
            $parts[] = 'Jurusan ' . $this->target_major;
        }

        return !empty($parts) ? implode(' - ', $parts) : 'Semua Siswa Terbina';
    }
}
