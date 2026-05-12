<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AlsStory;
use Illuminate\Http\Request;

class AlsStoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AlsStory::query();

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by Keyword Search (Title or Content)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Paginate and retain search filters in the URL (Set to 10 for consistency)
        $items = $query->latest()->paginate(5)->withQueryString();
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