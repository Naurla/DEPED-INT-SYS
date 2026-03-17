<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteLogo extends Model
{
    protected $fillable = ['name', 'image_path', 'position', 'order', 'is_active'];
}