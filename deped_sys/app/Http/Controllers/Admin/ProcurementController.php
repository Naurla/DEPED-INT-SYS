<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;     

class ProcurementController extends Controller
{
    // Helper to format the category name nicely for the View
    private function getCategoryTitle($category)
    {
        $titles = [
            'bid-opportunities' => 'Bid Opportunities',
            'apcpi' => 'Agency Procurement Compliance and Performance Indicators',
            'app-cse' => 'Annual Procurement Plan – Common User Supplies',
            'app-non-cse' => 'Annual Procurement Plan – Non CSE',
            'award-notices' => 'Award Notices',
            'pmr' => 'Procurement Monitoring Report',
            'pre-bid-minutes' => 'Minutes of Pre-Bid Conference',
        ];
        return $titles[$category] ?? 'Procurement Documents';
    }

    public function index(Request $request, $category)
    {
        $categoryTitle = $this->getCategoryTitle($category);
        
        $query = BidOpportunity::where('category', $category)->latest();
        
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $opportunities = $query->paginate(10);

        return view('admin.procurement.index', compact('opportunities', 'category', 'categoryTitle'));
    }

    public function store(Request $request, $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'jpeg_file' => 'required_without:pdf_file|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'pdf_file' => 'required_without:jpeg_file|mimes:pdf|max:10240',
        ]);

        // Dynamically save to a folder based on the category!
        $folderPath = 'procurement/' . $category . '/' . now()->format('F_Y');

        $jpegPath = $request->hasFile('jpeg_file') ? $request->file('jpeg_file')->store($folderPath, 'public') : null;
        $pdfPath = $request->hasFile('pdf_file') ? $request->file('pdf_file')->store($folderPath, 'public') : null;

        BidOpportunity::create([
            'title' => $request->title,
            'description' => $request->description,
            'jpeg_path' => $jpegPath,
            'pdf_path' => $pdfPath,
            'category' => $category, // Assign the dynamic category
        ]);

        return redirect()->route('admin.procurement.index', $category)
                         ->with('success', \Illuminate\Support\Str::singular($this->getCategoryTitle($category)) . ' uploaded successfully.');
    }

    // --- MISSING UPDATE METHOD ADDED HERE ---
    public function update(Request $request, $category, $id)
    {
        $opportunity = BidOpportunity::where('category', $category)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            // Note: Files are nullable here because the user might just be updating the text
            'jpeg_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $folderPath = 'procurement/' . $category . '/' . now()->format('F_Y');
        
        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Replace Image if a new one is uploaded
        if ($request->hasFile('jpeg_file')) {
            // Delete old file from local storage
            if ($opportunity->jpeg_path) {
                Storage::disk('public')->delete($opportunity->jpeg_path);
            }
            $dataToUpdate['jpeg_path'] = $request->file('jpeg_file')->store($folderPath, 'public');
        }

        // Replace PDF if a new one is uploaded
        if ($request->hasFile('pdf_file')) {
            // Delete old file from local storage
            if ($opportunity->pdf_path) {
                Storage::disk('public')->delete($opportunity->pdf_path);
            }
            $dataToUpdate['pdf_path'] = $request->file('pdf_file')->store($folderPath, 'public');
        }

        // Apply Updates
        $opportunity->update($dataToUpdate);

        return redirect()->route('admin.procurement.index', $category)
                         ->with('success', \Illuminate\Support\Str::singular($this->getCategoryTitle($category)) . ' updated successfully.');
    }
    // ----------------------------------------

    public function destroy($category, $id)
    {
        $opportunity = BidOpportunity::findOrFail($id);

        if ($opportunity->jpeg_path) Storage::disk('public')->delete($opportunity->jpeg_path);
        if ($opportunity->pdf_path) Storage::disk('public')->delete($opportunity->pdf_path);

        $opportunity->delete();

        return redirect()->route('admin.procurement.index', $category)
                         ->with('success', 'Document deleted successfully.');
    }
}