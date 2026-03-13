<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model {
    use HasFactory;
    protected $table = 'k12_modules';
    protected $fillable = ['title', 'description', 'file_path', 'file_type', 'image_path'];
}