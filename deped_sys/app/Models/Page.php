<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    // Updated fillable array to use featured_videos
    protected $fillable = [
        'parent_id', 
        'title', 
        'slug', 
        'content', 
        'layout_template', 
        'show_in_nav',
        'featured_videos' 
    ];

    // Cast the JSON column to a PHP array automatically
    protected $casts = [
        'featured_videos' => 'array',
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // --- RELATIONS FOR HIERARCHY ---
    
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->with('children');
    }
}