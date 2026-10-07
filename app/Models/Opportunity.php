<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = [
        'code',
        'partner_id',
        'title',
        'type',
        'description',
        'requirements',
        'target_audience',
        'deadline',
        'quota',
        'registration_link',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function registrations()
    {
        return $this->hasMany(OpportunityRegistration::class);
    }
}
