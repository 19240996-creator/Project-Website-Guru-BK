<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerActivity extends Model
{
    protected $fillable = [
        'code',
        'partner_id',
        'title',
        'activity_type',
        'date',
        'start_time',
        'end_time',
        'room_location',
        'target_class_id',
        'target_class_ids',
        'max_participants',
        'pic_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'target_class_ids' => 'array',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function targetClass()
    {
        return $this->belongsTo(StudentClass::class, 'target_class_id');
    }

    /**
     * Check if this activity conflicts with existing activities.
     */
    public static function checkConflicts(string $date, string $startTime, string $endTime, string $room, $classIds = null, ?int $ignoreId = null)
    {
        $query = self::where('date', $date)
            ->whereNotIn('status', ['dibatalkan'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($sub) use ($startTime, $endTime) {
                      $sub->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                  });
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $conflicts = [];
        $candidates = $query->get();

        $checkClasses = is_array($classIds) ? $classIds : ($classIds ? [$classIds] : []);

        foreach ($candidates as $cand) {
            if (strcasecmp($cand->room_location, $room) === 0) {
                $conflicts[] = "Benturan Ruangan: Ruang {$room} sudah digunakan untuk '{$cand->title}' ({$cand->start_time} - {$cand->end_time}).";
            }
            if (!empty($checkClasses)) {
                $candClassIds = [];
                if ($cand->target_class_id) {
                    $candClassIds[] = (int) $cand->target_class_id;
                }
                if (!empty($cand->target_class_ids) && is_array($cand->target_class_ids)) {
                    foreach ($cand->target_class_ids as $cid) {
                        $candClassIds[] = (int) $cid;
                    }
                }
                $candClassIds = array_unique($candClassIds);

                foreach ($checkClasses as $cid) {
                    if (in_array((int) $cid, $candClassIds)) {
                        $targetCls = StudentClass::find($cid);
                        $className = $targetCls ? $targetCls->name : "Kelas target";
                        $conflicts[] = "Benturan Jadwal Kelas: {$className} sudah memiliki agenda '{$cand->title}' pada jam tersebut.";
                        break;
                    }
                }
            }
        }

        return $conflicts;
    }
}
