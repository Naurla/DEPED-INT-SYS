<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AlsImplementer;
use Illuminate\Http\Request;

class AlsImplementerController extends Controller
{
    public function index()
    {
        $items = AlsImplementer::latest()->paginate(5);
        $type_name = 'Featured ALS Implementers';
        
        return view('frontend.als_implementers.index', compact('items', 'type_name'));
    }

    public function show($id)
    {
        $item = AlsImplementer::findOrFail($id);
        $type_name = 'Featured ALS Implementer';
        
        return view('frontend.als_implementers.show', compact('item', 'type_name'));
    }
}