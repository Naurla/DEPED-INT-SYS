<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Sgod;

class SgodController extends Controller
{
    public function index()
    {
        // Fetch the charts (latest first)
        $items = Sgod::latest()->get(); 
        
        return view('frontend.sgod.index', compact('items'));
    }
}