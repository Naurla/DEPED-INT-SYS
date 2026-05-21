<?php

namespace App\Models;

use Carbon\Carbon; // Ensure Carbon is imported
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BidOpportunity extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'jpeg_path',
        'pdf_path',
        'excel_path',
        'category',
        'date', // Added 'date'
    ];

    public function getDisplayTitleAttribute()
    {
        // Use the custom date if provided, otherwise fallback to the created_at date
        $dateToFormat = $this->date ? $this->date : $this->created_at;
        $formattedDate = Carbon::parse($dateToFormat)->format('F d, Y');
        
        return "{$formattedDate} - {$this->title}";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}