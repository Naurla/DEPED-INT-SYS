<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisionMission extends Model
{
    // Add 'sections' to your fillable array
    protected $fillable = ['vision', 'mission', 'core_values', 'mandate', 'sections']; 

    // Cast the JSON column to an array automatically
    protected $casts = [
        'sections' => 'array',
    ];
}