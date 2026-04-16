<?php

// Location: app/Models/SeniorHighContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeniorHighContent extends Model
{
    protected $fillable = ['title', 'content', 'csv_path', 'school_type'];
}