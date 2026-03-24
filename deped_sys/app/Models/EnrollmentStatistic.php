<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentStatistic extends Model
{
    protected $fillable = [
        'title', 
        'school_year', 
        'content', 
        'image_path', 
        'file_path'
    ];
}