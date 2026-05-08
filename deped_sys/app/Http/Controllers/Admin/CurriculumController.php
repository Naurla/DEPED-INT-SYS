<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CurriculumPage;
use App\Models\LearningStrand;
use App\Models\LearningMaterial;
use App\Models\CurriculumGuide;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $pageData = CurriculumPage::firstOrCreate([]);
        
        // --- 1. Learning Strands Query with Filters ---
        $strandQuery = LearningStrand::with('materials');
        
        if ($request->filled('strand_search')) {
            $search = $request->strand_search;
            $strandQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('strand_sort')) {
            if ($request->strand_sort === 'a_z') $strandQuery->orderBy('name', 'asc');
            elseif ($request->strand_sort === 'z_a') $strandQuery->orderBy('name', 'desc');
            elseif ($request->strand_sort === 'newest') $strandQuery->latest('created_at');
            elseif ($request->strand_sort === 'oldest') $strandQuery->oldest('created_at');
        } else {
            $strandQuery->orderBy('sort_order'); // Default behavior
        }
        
        $strands = $strandQuery->get();

        // --- 2. Curriculum Guides Query with Filters ---
        $guideQuery = CurriculumGuide::query();
        
        if ($request->filled('guide_search')) {
            $search = $request->guide_search;
            $guideQuery->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('guide_sort')) {
            if ($request->guide_sort === 'a_z') $guideQuery->orderBy('title', 'asc');
            elseif ($request->guide_sort === 'z_a') $guideQuery->orderBy('title', 'desc');
            elseif ($request->guide_sort === 'oldest') $guideQuery->oldest('created_at');
            elseif ($request->guide_sort === 'newest') $guideQuery->latest('created_at');
        } else {
            $guideQuery->latest('created_at'); // Default behavior
        }

        $guides = $guideQuery->get();

        return view('admin.curriculum.index', compact('pageData', 'strands', 'guides'));
    }

    public function updatePage(Request $request)
    {
        $request->validate([
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $page = CurriculumPage::firstOrCreate([]);

        if ($request->input('remove_image') == 1) {
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
                $page->banner_image_path = null;
            }
        }

        if ($request->hasFile('banner_image')) {
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
            }
            $path = $request->file('banner_image')->store('curriculum/banners', 'public');
            $page->banner_image_path = $path;
        }

        $page->save();

        return back()->with('success', 'Curriculum banner updated successfully.');
    }

    public function storeStrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:learning_strands,name',
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|array',
            'content_description.*' => 'nullable|string',
        ], [
            'name.unique' => 'A strand with this title already exists.'
        ]);

        $descriptions = $request->content_description 
            ? array_values(array_filter($request->content_description, fn($val) => !is_null($val) && trim($val) !== '')) 
            : [];

        LearningStrand::create([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $descriptions,
        ]);
        
        return back()->with('success', 'Strand added successfully.');
    }

    public function updateStrand(Request $request, LearningStrand $strand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:learning_strands,name,' . $strand->id,
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|array',
            'content_description.*' => 'nullable|string',
        ], [
            'name.unique' => 'A strand with this title already exists.'
        ]);

        $descriptions = $request->content_description 
            ? array_values(array_filter($request->content_description, fn($val) => !is_null($val) && trim($val) !== '')) 
            : [];

        $strand->update([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $descriptions,
        ]);
        
        return back()->with('success', 'Strand updated successfully.');
    }

    public function destroyStrand(LearningStrand $strand)
    {
        foreach ($strand->materials as $material) {
            Storage::disk('public')->delete($material->file_path);
        }
        $strand->delete();
        return back()->with('success', 'Strand and its materials deleted.');
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'learning_strand_id' => 'required|exists:learning_strands,id',
            'title' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10000', 
        ]);

        $path = $request->file('pdf_file')->store('curriculum/materials', 'public');

        LearningMaterial::create([
            'learning_strand_id' => $request->learning_strand_id,
            'title' => $request->title,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Material uploaded successfully.');
    }

    public function destroyMaterial(LearningMaterial $material)
    {
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Material deleted.');
    }

    public function storeGuide(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:curriculum_guides,title',
            'link' => 'required|url|max:255'
        ], [
            'title.unique' => 'A curriculum guide with this title already exists.'
        ]);

        CurriculumGuide::create($request->all());

        return redirect()->back()->with('success', 'Curriculum Guide added successfully!');
    }

    public function updateGuide(Request $request, CurriculumGuide $guide)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:curriculum_guides,title,' . $guide->id,
            'link' => 'required|url|max:255'
        ], [
            'title.unique' => 'A curriculum guide with this title already exists.'
        ]);

        $guide->update($request->all());

        return redirect()->back()->with('success', 'Curriculum Guide updated successfully!');
    }

    public function destroyGuide(CurriculumGuide $guide)
    {
        $guide->delete();
        return redirect()->back()->with('success', 'Curriculum Guide deleted successfully!');
    }
}