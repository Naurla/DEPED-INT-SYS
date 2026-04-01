<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;     

class ProcurementController extends Controller
{
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
            'date' => 'nullable|date', // Validate date
        ]);

        $folderPath = 'procurement/' . $category . '/' . now()->format('F_Y');

        $jpegPath = null;
        if ($request->hasFile('jpeg_file')) {
            $imgFile = $request->file('jpeg_file');
            $imgFilename = $imgFile->getClientOriginalName();
            $jpegPath = $imgFile->storeAs($folderPath, $imgFilename, 'public');
        }

        $pdfPath = null;
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $pdfPath = $pdfFile->storeAs($folderPath, $pdfFilename, 'public');
        }

        BidOpportunity::create([
            'title' => $request->title,
            'description' => $request->description,
            'jpeg_path' => $jpegPath,
            'pdf_path' => $pdfPath,
            'category' => $category,
            'date' => $request->date ?: now()->toDateString(), // Fallback to current date if left blank
        ]);

        return redirect()->route('admin.procurement.index', $category)
                         ->with('success', Str::singular($this->getCategoryTitle($category)) . ' uploaded successfully.');
    }

    public function update(Request $request, $category, $id)
    {
        $opportunity = BidOpportunity::where('category', $category)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'jpeg_file' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'date' => 'nullable|date', // Validate date
        ]);

        $folderPath = 'procurement/' . $category . '/' . now()->format('F_Y');
        
        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date ?: now()->toDateString(), // Fallback to current date if left blank
        ];

        if ($request->input('remove_image') == '1') {
            if ($opportunity->jpeg_path) Storage::disk('public')->delete($opportunity->jpeg_path);
            $dataToUpdate['jpeg_path'] = null;
        }

        if ($request->input('remove_pdf') == '1') {
            if ($opportunity->pdf_path) Storage::disk('public')->delete($opportunity->pdf_path);
            $dataToUpdate['pdf_path'] = null;
        }

        if ($request->hasFile('jpeg_file')) {
            if ($opportunity->jpeg_path) Storage::disk('public')->delete($opportunity->jpeg_path);
            
            $imgFile = $request->file('jpeg_file');
            $imgFilename = $imgFile->getClientOriginalName();
            $dataToUpdate['jpeg_path'] = $imgFile->storeAs($folderPath, $imgFilename, 'public');
        }

        if ($request->hasFile('pdf_file')) {
            if ($opportunity->pdf_path) Storage::disk('public')->delete($opportunity->pdf_path);
            
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $dataToUpdate['pdf_path'] = $pdfFile->storeAs($folderPath, $pdfFilename, 'public');
        }

        $opportunity->update($dataToUpdate);

        return redirect()->route('admin.procurement.index', $category)
                         ->with('success', Str::singular($this->getCategoryTitle($category)) . ' updated successfully.');
    }

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