<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Osds extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'image_path'
    ];
}