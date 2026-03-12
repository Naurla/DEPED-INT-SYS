<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch the bid opportunities from the database, newest first
        $bidOpportunities = BidOpportunity::latest()->paginate(10);

        // Return the view and pass the data to it
        return view('admin.bid-opportunities.index', compact('bidOpportunities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            // Ensures at least ONE file is uploaded, but not necessarily both
            'jpeg_file' => 'required_without:pdf_file|image|mimes:jpeg,jpg|max:5120',
            'pdf_file' => 'required_without:jpeg_file|mimes:pdf|max:10240',
        ]);

        // Create a folder path based on the current month and year
        $folderPath = 'bid_opportunities/' . now()->format('F_Y');

        // Check if the file exists in the request before trying to store it
        $jpegPath = $request->hasFile('jpeg_file') 
            ? $request->file('jpeg_file')->store($folderPath, 'public') 
            : null;
            
        $pdfPath = $request->hasFile('pdf_file') 
            ? $request->file('pdf_file')->store($folderPath, 'public') 
            : null;

        // Create Local Record
        BidOpportunity::create([
            'title' => $request->title,
            'description' => $request->description,
            'jpeg_path' => $jpegPath,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity uploaded successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bidOpportunity = BidOpportunity::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            // Files are nullable here because the user might just be updating the title/description
            'jpeg_file' => 'nullable|image|mimes:jpeg,jpg|max:5120',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $folderPath = 'bid_opportunities/' . now()->format('F_Y');
        
        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Replace Image if a new one is uploaded
        if ($request->hasFile('jpeg_file')) {
            // Delete old file from local storage
            if ($bidOpportunity->jpeg_path) {
                Storage::disk('public')->delete($bidOpportunity->jpeg_path);
            }
            $dataToUpdate['jpeg_path'] = $request->file('jpeg_file')->store($folderPath, 'public');
        }

        // Replace PDF if a new one is uploaded
        if ($request->hasFile('pdf_file')) {
            // Delete old file from local storage
            if ($bidOpportunity->pdf_path) {
                Storage::disk('public')->delete($bidOpportunity->pdf_path);
            }
            $dataToUpdate['pdf_path'] = $request->file('pdf_file')->store($folderPath, 'public');
        }

        // Apply Updates
        $bidOpportunity->update($dataToUpdate);

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bidOpportunity = BidOpportunity::findOrFail($id);

        // Delete Image from local storage
        if ($bidOpportunity->jpeg_path) {
            Storage::disk('public')->delete($bidOpportunity->jpeg_path);
        }

        // Delete PDF from local storage
        if ($bidOpportunity->pdf_path) {
            Storage::disk('public')->delete($bidOpportunity->pdf_path);
        }

        // Delete database record
        $bidOpportunity->delete();

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity deleted successfully.');
    }
}