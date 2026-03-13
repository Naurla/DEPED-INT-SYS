<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumGuide extends Model {
    use HasFactory;
    protected $fillable = ['title', 'link'];
}