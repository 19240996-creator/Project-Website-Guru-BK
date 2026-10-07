<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'semester', 'is_active'];

    public function classes()
    {
        return $this->hasMany(StudentClass::class);
    }
}
