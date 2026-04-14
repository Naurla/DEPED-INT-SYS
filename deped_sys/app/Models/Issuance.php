<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    protected $fillable = ['title', 'description', 'type', 'pdf_path', 'image_path', 'date', 'link'];// Added 'date'

    public function getDisplayTitleAttribute()
    {
        // Use the custom date if provided, otherwise fallback to the created_at date
        $dateToFormat = $this->date ? $this->date : $this->created_at;
        $formattedDate = Carbon::parse($dateToFormat)->format('F d, Y');
        
        return "{$formattedDate} - {$this->title}";
    }
}