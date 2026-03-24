<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DivisionStructure;
use Illuminate\Http\Request;

class DivisionStructureController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all structures for the sidebar menu
        $structures = DivisionStructure::orderBy('order_no', 'asc')->get();
        
        // Determine the active structure to display in the main content area
        $activeStructure = null;
        
        if ($request->has('id')) {
            // If user clicked a specific division, find it
            $activeStructure = $structures->where('id', $request->id)->first();
        }
        
        // If no division is selected (or first time visiting), default to the first one
        if (!$activeStructure && $structures->count() > 0) {
            $activeStructure = $structures->first();
        }
        
        return view('frontend.division_structures.index', compact('structures', 'activeStructure'));
    }
}