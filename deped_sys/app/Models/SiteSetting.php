<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'header_title', 'footer_about', 'contact_email', 'contact_phone', 'address'
    ];

    // Tell Laravel these are arrays
    protected $casts = [
        'contact_email' => 'array',
        'contact_phone' => 'array',
        'address' => 'array',
        'footer_sections' => 'array',
    ];
}