<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningStrand extends Model
{
    // Added content_title and content_description
    protected $fillable = ['name', 'content_title', 'content_description', 'sort_order'];

    // Cast content_description to an array so it can store multiple bullet points
    protected $casts = [
        'content_description' => 'array',
    ];

    public function materials()
    {
        return $this->hasMany(LearningMaterial::class)->orderBy('title');
    }
}