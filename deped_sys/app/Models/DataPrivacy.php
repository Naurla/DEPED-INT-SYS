<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPrivacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'notice',
    ];
}