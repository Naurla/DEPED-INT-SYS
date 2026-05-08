<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSectionController extends Controller
{
    public function index(Request $request)
    {
        $query = PageSection::query();

        // 1. Search Filter (Title & Content)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // 2. Location Filter
        if ($request->filled('location')) {
            $query->where('display_location', $request->location);
        }

        // 3. Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 4. Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->latest('created_at');
                    break;
                case 'oldest':
                    $query->oldest('created_at');
                    break;
                case 'order_asc':
                    $query->orderBy('sort_order', 'asc');
                    break;
                case 'order_desc':
                    $query->orderBy('sort_order', 'desc');
                    break;
            }
        } else {
            // Default sorting groups them by page, then sorts by order
            $query->orderBy('display_location', 'asc')->orderBy('sort_order', 'asc');
        }

        // withQueryString() ensures filters stay active when paginating
        $sections = $query->paginate(10)->withQueryString();
        $dynamicPages = Page::all();
        
        // Fetch unique existing locations and types for the dropdowns
        $locations = PageSection::select('display_location')->distinct()->pluck('display_location');
        $types = PageSection::select('type')->distinct()->pluck('type');
        
        return view('admin.page_sections.index', compact('sections', 'dynamicPages', 'locations', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_location' => 'required|string',
            'type' => 'required|string', // Removed strict 'in:' so it accepts any widget type
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        $data = $request->only(['display_location', 'type', 'title', 'content', 'sort_order', 'is_active']);

        if ($request->type === 'banner' && $request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('page_sections', $filename, 'public');
        }

        PageSection::create($data);

        return back()->with('success', 'Content block added successfully!');
    }

    public function update(Request $request, $id)
    {
        $section = PageSection::findOrFail($id);

        $request->validate([
            'display_location' => 'required|string',
            'type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        $data = $request->only(['display_location', 'type', 'title', 'content', 'sort_order', 'is_active']);

        // Handle Image Replacement for Banners
        if ($request->type === 'banner' && $request->hasFile('image')) {
            if ($section->image_path) {
                Storage::disk('public')->delete($section->image_path);
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image_path'] = $file->storeAs('page_sections', $filename, 'public');
        }

        // If they changed a Banner to Text or a Widget, delete the old image
        if ($request->type !== 'banner' && $section->image_path) {
            Storage::disk('public')->delete($section->image_path);
            $data['image_path'] = null;
        }

        $section->update($data);

        return back()->with('success', 'Content block updated successfully!');
    }

    public function destroy($id)
    {
        $section = PageSection::findOrFail($id);
        
        if ($section->image_path) {
            Storage::disk('public')->delete($section->image_path);
        }
        
        $section->delete();
        return back()->with('success', 'Content block deleted!');
    }
}