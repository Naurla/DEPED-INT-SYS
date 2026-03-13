<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningStrand extends Model
{
    // Added content_title and content_description
    protected $fillable = ['name', 'content_title', 'content_description', 'sort_order'];

    public function materials()
    {
        return $this->hasMany(LearningMaterial::class)->orderBy('title');
    }
}