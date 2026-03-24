<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlsStory extends Model
{
    protected $fillable = [
        'title', 
        'content', 
        'image_path', 
        'file_path'
    ];
}