<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionAssignment extends Model
{
    // Added employee_position
    protected $fillable = ['position_id', 'slot_index', 'employee_name', 'employee_position', 'employee_image'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}