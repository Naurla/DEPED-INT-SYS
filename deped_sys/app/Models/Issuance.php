<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    protected $fillable = ['title', 'description', 'type', 'pdf_path', 'image_path'];
    public function getDisplayTitleAttribute()
        {
    $formattedDate = Carbon::parse($this->date)->format('F d, Y');
    return "{$formattedDate} - {$this->title}";
        }
}
