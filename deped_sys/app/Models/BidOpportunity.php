<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'jpeg_path',
        'pdf_path',
    ];
}