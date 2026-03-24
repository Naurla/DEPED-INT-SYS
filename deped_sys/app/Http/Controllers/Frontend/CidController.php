<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cid;

class CidController extends Controller
{
    public function index()
    {
        $items = Cid::latest()->get(); 
        
        return view('frontend.cid.index', compact('items'));
    }
}