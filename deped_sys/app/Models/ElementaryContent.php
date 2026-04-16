<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElementaryContent extends Model
{
    protected $fillable = ['title', 'content', 'csv_path', 'school_type'];
}