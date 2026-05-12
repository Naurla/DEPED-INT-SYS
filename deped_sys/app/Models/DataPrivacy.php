<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPrivacy extends Model
{
    protected $fillable = ['notice', 'sections']; // Add sections

    // Cast the JSON column to an array automatically
    protected $casts = [
        'sections' => 'array',
    ];
}