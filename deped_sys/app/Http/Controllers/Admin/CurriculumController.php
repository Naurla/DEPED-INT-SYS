<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CurriculumPage;
use App\Models\LearningStrand;
use App\Models\LearningMaterial;

class CurriculumController extends Controller
{
    public function index()
    {
        // Get or create the single curriculum page record (removed description default)
        $pageData = CurriculumPage::firstOrCreate([]);
        
        $strands = LearningStrand::with('materials')->orderBy('sort_order')->get();

        return view('admin.curriculum.index', compact('pageData', 'strands'));
    }

    public function updatePage(Request $request)
    {
        // Validating the image and the new remove_image flag (description removed)
        $request->validate([
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $page = CurriculumPage::firstOrCreate([]);

        // Handle Image Removal (Triggered when the "Remove Picture" button is clicked)
        if ($request->input('remove_image') == 1) {
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
                $page->banner_image_path = null;
            }
        }

        // Handle Image Upload & Replace
        if ($request->hasFile('banner_image')) {
            // Delete old image if it exists before saving the new one
            if ($page->banner_image_path) {
                Storage::disk('public')->delete($page->banner_image_path);
            }
            // Store new image
            $path = $request->file('banner_image')->store('curriculum/banners', 'public');
            $page->banner_image_path = $path;
        }

        $page->save();

        return back()->with('success', 'Curriculum banner updated successfully.');
    }

    public function storeStrand(Request $request)
    {
        // Added validation for the new fields
        $request->validate([
            'name' => 'required|string|max:255',
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|string',
        ]);

        // Included new fields in the creation array
        LearningStrand::create([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $request->content_description,
        ]);
        
        return back()->with('success', 'Strand added successfully.');
    }

    public function updateStrand(Request $request, LearningStrand $strand)
    {
        // Added validation for the new fields
        $request->validate([
            'name' => 'required|string|max:255',
            'content_title' => 'nullable|string|max:255',
            'content_description' => 'nullable|string',
        ]);

        // Included new fields in the update array
        $strand->update([
            'name' => $request->name,
            'content_title' => $request->content_title,
            'content_description' => $request->content_description,
        ]);
        
        return back()->with('success', 'Strand updated successfully.');
    }

    public function destroyStrand(LearningStrand $strand)
    {
        // Delete all associated PDF files first
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
            'pdf_file' => 'required|file|mimes:pdf|max:10000', // Max 10MB PDF
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
        // Delete the physical file, then the database record
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Material deleted.');
    }
}