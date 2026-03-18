<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    // Added parent_id and layout_template
    protected $fillable = ['parent_id', 'title', 'slug', 'content', 'layout_template', 'show_in_nav'];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // --- NEW RELATIONS FOR HIERARCHY ---
    
    // Get the parent category of this page
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    // Get all sub-categories belonging to this page (with infinite nesting)
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->with('children');
    }
}