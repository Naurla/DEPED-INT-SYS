<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
            'content' => 'nullable', 
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
        $allPages = Page::where('id', '!=', $page->id)->get();
        return view('admin.pages.edit', compact('page', 'allPages'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', 
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

    // --- CKEDITOR IMAGE UPLOAD METHOD ---
    public function uploadImage(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                
                // Using uniqid() ensures no weird filename characters break the upload
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
                // Store the image in the public disk
                $file->storeAs('public/pages/images', $fileName);
        
                // Generate the public URL
                $url = asset('storage/pages/images/' . $fileName);
        
                // Return exactly what CKEditor expects
                return response()->json([
                    'uploaded' => 1,
                    'fileName' => $fileName,
                    'url' => $url
                ]);
            }
            
            return response()->json(['uploaded' => 0, 'error' => ['message' => 'No file found in request.']]);
        } catch (\Exception $e) {
            // If anything goes wrong (like folder permissions), return the exact error
            return response()->json(['uploaded' => 0, 'error' => ['message' => $e->getMessage()]]);
        }
    }
}