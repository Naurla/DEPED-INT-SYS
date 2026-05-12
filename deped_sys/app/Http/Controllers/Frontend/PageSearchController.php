<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageSearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('q');

        // If no keyword is provided, return an empty list
        if (empty($keyword)) {
            $pages = collect(); 
        } else {
            // Search BOTH the main Page content AND its dynamic Page Sections
            $pages = Page::where(function($query) use ($keyword) {
                
                // 1. Search the main page title and content
                $query->where('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%")
                      
                // 2. ALSO search inside any Page Sections assigned to this page
                      ->orWhereExists(function ($subquery) use ($keyword) {
                          $subquery->from('page_sections')
                                   // This links the section to the page using your "page:slug" format
                                   ->whereRaw("page_sections.display_location = CONCAT('page:', pages.slug)")
                                   ->where('is_active', 1) // Only check active sections
                                   ->where(function($q) use ($keyword) {
                                       $q->where('title', 'like', "%{$keyword}%")
                                         ->orWhere('content', 'like', "%{$keyword}%");
                                   });
                      });
                      
            })
            // 🟢 NEW: Ensure the page AND all of its ancestors are active
            ->where('show_in_nav', 1) // Page itself must be active
            ->whereDoesntHave('parent', function($q) {
                $q->where('show_in_nav', 0); // Parent cannot be inactive
            })
            ->whereDoesntHave('parent.parent', function($q) {
                $q->where('show_in_nav', 0); // Grandparent cannot be inactive
            })
            ->whereDoesntHave('parent.parent.parent', function($q) {
                $q->where('show_in_nav', 0); // Great-Grandparent cannot be inactive
            })
            ->paginate(5)->withQueryString();
        }

        return view('frontend.pages.search-results', compact('pages', 'keyword'));
    }
}