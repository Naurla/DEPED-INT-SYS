<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advisory extends Model
{
    use HasFactory;

    // This allows the Controller to save data into these columns
    protected $fillable = [
        'title',
        'image_path',
        'pdf_path'
    ];
}