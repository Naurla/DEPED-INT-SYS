<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        
        // Construct the view name based on the admin's choice
        $viewName = 'frontend.pages.' . $page->layout_template;

        // Check if that specific layout file actually exists
        if (view()->exists($viewName)) {
            return view($viewName, compact('page'));
        }

        // Fallback to the default layout if the chosen one is missing
        return view('frontend.pages.default', compact('page'));
    }
}