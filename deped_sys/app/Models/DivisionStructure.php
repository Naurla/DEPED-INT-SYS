<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'order_no',
        'descriptions',
        'main_photo',
        'gallery_images',
        'pdf_documents',
    ];

    // This automatically converts the JSON from the database back into PHP arrays
    protected $casts = [
        'descriptions' => 'array',
        'gallery_images' => 'array',
        'pdf_documents' => 'array',
    ];
}