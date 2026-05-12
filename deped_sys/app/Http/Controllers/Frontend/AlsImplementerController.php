<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AlsImplementer;
use Illuminate\Http\Request;

class AlsImplementerController extends Controller
{
    public function index(Request $request)
    {
        $query = AlsImplementer::query();

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