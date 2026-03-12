<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningStrand extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function materials()
    {
        return $this->hasMany(LearningMaterial::class)->orderBy('title');
    }
}