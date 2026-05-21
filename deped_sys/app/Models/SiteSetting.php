<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SiteSetting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'header_title', 'footer_about', 'contact_email', 'contact_phone', 'address', 'qr_link', 'is_maintenance_mode','disabled_pages', // <-- Added qr_link
    ];

    protected $casts = [
        'contact_email' => 'array',
        'contact_phone' => 'array',
        'address' => 'array',
        'footer_sections' => 'array',
        'disabled_pages' => 'array',
        'is_maintenance_mode' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}