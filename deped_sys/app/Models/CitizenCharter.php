<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenCharter extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'file_path',
        'file_name',
        'links',
    ];

    // This automatically converts the JSON column back and forth to a PHP array
    protected $casts = [
        'links' => 'array',
    ];
}