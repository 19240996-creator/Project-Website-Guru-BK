<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'code',
        'type',
        'category',
        'name',
        'city',
        'address',
        'website',
        'contact_person',
        'phone',
        'email',
        'partnership_doc_number',
        'partnership_status',
        'partnership_start_date',
        'partnership_end_date',
        'notes',
    ];

    protected $casts = [
        'partnership_start_date' => 'date',
        'partnership_end_date' => 'date',
    ];

    public function activities()
    {
        return $this->hasMany(PartnerActivity::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
}
