<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    protected $fillable = ['title', 'description', 'type', 'pdf_path', 'image_path'];
}
