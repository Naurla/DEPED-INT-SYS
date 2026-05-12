<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qms extends Model
{
    // Add 'sections' to your fillable array
    protected $fillable = ['scope', 'policy', 'objective', 'sections']; 

    // Cast the JSON column to an array automatically
    protected $casts = [
        'sections' => 'array',
    ];
}