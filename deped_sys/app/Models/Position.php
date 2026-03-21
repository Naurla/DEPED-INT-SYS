<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'slots_count', 'parent_id', 'order_index'];

    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Position::class, 'parent_id')->orderBy('order_index');
    }

    public function assignments()
    {
        return $this->hasMany(PositionAssignment::class)->orderBy('slot_index');
    }
}