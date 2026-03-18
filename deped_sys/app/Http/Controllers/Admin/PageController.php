<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
   public function index()
{
    // Fetch ALL pages and load their parent so we can show the hierarchy
    $pages = Page::with('parent')->get();
    return view('admin.pages.index', compact('pages'));
}

    public function create()
    {
        $allPages = Page::all(); // To populate the Parent Dropdown
        return view('admin.pages.create', compact('allPages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', // Changed from 'required'
            'layout_template' => 'required|string'
        ]);

        Page::create([
            'parent_id' => $request->parent_id ?: null,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        // Get all pages EXCEPT the current one (a page cannot be its own parent)
        $allPages = Page::where('id', '!=', $page->id)->get();
        return view('admin.pages.edit', compact('page', 'allPages'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', // Changed from 'required'
            'layout_template' => 'required|string'
        ]);

        $page->update([
            'parent_id' => $request->parent_id ?: null,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}