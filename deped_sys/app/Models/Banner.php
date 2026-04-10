<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    // Added sort_order and is_active
    protected $fillable = ['image_path', 'sort_order', 'is_active'];
}