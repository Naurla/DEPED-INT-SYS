<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionAssignment extends Model
{
    protected $fillable = ['position_id', 'slot_index', 'employee_name', 'employee_image'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}