<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSectionController extends Controller
{
    public function index()
    {
        $sections = PageSection::orderBy('display_location', 'asc')->orderBy('sort_order', 'asc')->get();
        $dynamicPages = Page::all();
        
        return view('admin.page_sections.index', compact('sections', 'dynamicPages'));
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
            $data['image_path'] = $request->file('image')->store('page_sections', 'public');
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
            $data['image_path'] = $request->file('image')->store('page_sections', 'public');
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