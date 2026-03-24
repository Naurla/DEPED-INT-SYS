<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AlsStory;
use Illuminate\Http\Request;

class AlsStoryController extends Controller
{
    public function index()
    {
        $items = AlsStory::latest()->paginate(10);
        $type_name = 'ALS Stories';
        
        return view('frontend.als_stories.index', compact('items', 'type_name'));
    }

    public function show($id)
    {
        $item = AlsStory::findOrFail($id);
        $type_name = 'ALS Story';
        
        return view('frontend.als_stories.show', compact('item', 'type_name'));
    }
}