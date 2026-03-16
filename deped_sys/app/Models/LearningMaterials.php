<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterials extends Model
{
    // Explicitly point to the K-12 table
    protected $table = 'k12_learning_materials';

    // Add the correct columns from your migration
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type'
    ];
}