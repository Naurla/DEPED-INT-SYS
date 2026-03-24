<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Osds;

class OsdsController extends Controller
{
    public function index()
    {
        $items = Osds::latest()->get(); 
        
        return view('frontend.osds.index', compact('items'));
    }
}