<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function counselings()
    {
        return $this->hasMany(Counseling::class, 'category_id');
    }
}
