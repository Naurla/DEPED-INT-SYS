<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sgod extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'image_path'
    ];
}