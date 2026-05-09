<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterials;
use Illuminate\Http\Request;

class LearningMaterialsController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningMaterials::query();

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

        // Paginate and retain search filters in the URL (Set to 10)
        $materials = $query->latest()->paginate(10)->withQueryString();
        
        return view('learning_materials.index', compact('materials'));
    }

    public function show(string $id)
    {
        $material = LearningMaterials::findOrFail($id);
        return view('learning_materials.show', compact('material'));
    }
}