<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CurriculumPage;
use App\Models\LearningStrand;
use App\Models\CurriculumGuide;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        // Get the first record for the main page content (Banner, etc.)
        $pageData = CurriculumPage::first(); 
        
        // Start queries
        $strandsQuery = LearningStrand::with('materials')->orderBy('sort_order');
        $guidesQuery = CurriculumGuide::query();

        // Apply Keyword Search to BOTH Strands and Guides
        if ($request->filled('search')) {
            $search = $request->search;

            // Filter Strands
            $strandsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content_title', 'like', "%{$search}%")
                  ->orWhere('content_description', 'like', "%{$search}%");
            });

            // Filter Guides
            $guidesQuery->where('title', 'like', "%{$search}%");
        }

        // Fetch the results
        $strands = $strandsQuery->get();
        $guides = $guidesQuery->get();

        return view('curriculum.index', compact('pageData', 'strands', 'guides'));
    }
}