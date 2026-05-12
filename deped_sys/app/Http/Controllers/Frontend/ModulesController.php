<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;

class ModulesController extends Controller {
    
    public function index(Request $request) {
        $query = Modules::query();

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by Keyword Search (Title or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginate and retain search filters in the URL (Increased to 10 for consistency)
        $items = $query->latest()->paginate(5)->withQueryString();
        
        return view('modules.index', compact('items'));
    }

    public function show($id) {
        $item = Modules::findOrFail($id);
        return view('modules.show', compact('item'));
    }
}