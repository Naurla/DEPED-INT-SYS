<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    protected $fillable = ['learning_strand_id', 'title', 'file_path'];

    public function strand()
    {
        return $this->belongsTo(LearningStrand::class);
    }
}