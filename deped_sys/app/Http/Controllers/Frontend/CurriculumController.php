<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CurriculumPage;
use App\Models\LearningStrand;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index()
    {
        // Get the first record for the main page content
        $pageData = CurriculumPage::first(); 
        
        // Eager load the materials to prevent N+1 query issues
        $strands = LearningStrand::with('materials')->orderBy('sort_order')->get();

        // Make sure to create this view file next!
        return view('curriculum.index', compact('pageData', 'strands'));
    }
}