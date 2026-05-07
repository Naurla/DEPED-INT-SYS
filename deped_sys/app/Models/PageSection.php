<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'display_location', 
        'type', 
        'title', 
        'content', 
        'image_path', 
        'sort_order', 
        'is_active'
    ];
}