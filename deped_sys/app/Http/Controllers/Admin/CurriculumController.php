<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CurriculumPage;
use App\Models\LearningStrand;
use App\Models\LearningMaterial;
use App\Models\CurriculumGuide; // This import is required

class CurriculumController extends Controller
{
    public function index()
    {
        // Get or create the single curriculum page record
        $pageData = CurriculumPage::firstOrCreate([]);
        
        $strands = LearningStrand::with('materials')->orderBy('sort_order')->get();
        
        // Fetch the guides for the new section
        $guides = CurriculumGuide::all();

        return view('admin.curriculum.index', compact('pageData', 'strands', 'guides'));
    }

    public function updatePage(Request $request)
    {
        $request->validate([
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $page = CurriculumPage::firstOrCreate([]);

        // Handle Image Removal
        if ($request->input('remove_image') == 1) {
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
                $page->banner_image_path = null;
            }
        }

        // Handle Image Upload & Replace
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
            'name' => 'required|string|max:255',
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|string',
        ]);

        LearningStrand::create([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $request->content_description,
        ]);
        
        return back()->with('success', 'Strand added successfully.');
    }

    public function updateStrand(Request $request, LearningStrand $strand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|string',
        ]);

        $strand->update([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $request->content_description,
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

    // ==========================================
    // NEW CURRICULUM GUIDES METHODS
    // ==========================================

    public function storeGuide(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|url|max:255'
        ]);

        CurriculumGuide::create($request->all());

        return redirect()->back()->with('success', 'Curriculum Guide added successfully!');
    }

    public function updateGuide(Request $request, CurriculumGuide $guide)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|url|max:255'
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