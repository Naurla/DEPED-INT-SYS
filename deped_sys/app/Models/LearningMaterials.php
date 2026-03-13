<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterials extends Model
{
    use HasFactory;

    // Optional: Explicitly define the table name if Laravel gets confused by the plural model name
    protected $table = 'k12_learning_materials';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
    ];
}