<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DivisionStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DivisionStructureController extends Controller
{
    public function index()
    {
        $structures = DivisionStructure::orderBy('order_no')->get();
        return view('admin.division_structures.index', compact('structures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'main_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pdf_documents.*' => 'nullable|mimes:pdf|max:10000',
        ]);

        $data = $request->except(['main_photo', 'pdf_documents']);
        $data['type'] = 'Division'; // Default

        if ($request->hasFile('main_photo')) {
            $data['main_photo'] = $request->file('main_photo')->store('division_photos', 'public');
        }

        $pdfs = [];
        if ($request->hasFile('pdf_documents')) {
            foreach ($request->file('pdf_documents') as $pdf) {
                $pdfs[] = [
                    'original_name' => $pdf->getClientOriginalName(),
                    'path' => $pdf->store('division_pdfs', 'public'),
                    'size' => round($pdf->getSize() / 1024 / 1024, 2) . ' MB'
                ];
            }
        }
        $data['pdf_documents'] = $pdfs;

        DivisionStructure::create($data);

        return redirect()->route('admin.division_structures.index')->with('success', 'Entry added successfully!');
    }

    public function edit(DivisionStructure $divisionStructure)
    {
        return view('admin.division_structures.edit', compact('divisionStructure'));
    }

    public function update(Request $request, DivisionStructure $divisionStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'main_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pdf_documents.*' => 'nullable|mimes:pdf|max:10000',
        ]);

        $data = $request->except(['main_photo', 'pdf_documents']);

        // Update Banner Photo (Replaces old one)
        if ($request->hasFile('main_photo')) {
            if ($divisionStructure->main_photo) Storage::disk('public')->delete($divisionStructure->main_photo);
            $data['main_photo'] = $request->file('main_photo')->store('division_photos', 'public');
        }

        // Add NEW PDFs to existing ones
        $pdfs = is_array($divisionStructure->pdf_documents) ? $divisionStructure->pdf_documents : [];
        if ($request->hasFile('pdf_documents')) {
            foreach ($request->file('pdf_documents') as $pdf) {
                $pdfs[] = [
                    'original_name' => $pdf->getClientOriginalName(),
                    'path' => $pdf->store('division_pdfs', 'public'),
                    'size' => round($pdf->getSize() / 1024 / 1024, 2) . ' MB'
                ];
            }
            $data['pdf_documents'] = $pdfs;
        }

        $divisionStructure->update($data);

        return redirect()->route('admin.division_structures.index')->with('success', 'Entry updated successfully!');
    }

    public function destroyPdf(DivisionStructure $divisionStructure, $pdfIndex)
    {
        $pdfs = $divisionStructure->pdf_documents;
        if (isset($pdfs[$pdfIndex])) {
            Storage::disk('public')->delete($pdfs[$pdfIndex]['path']);
            unset($pdfs[$pdfIndex]);
            // Re-index array so JSON stays valid
            $divisionStructure->update(['pdf_documents' => array_values($pdfs)]);
        }
        return back()->with('success', 'PDF deleted successfully.');
    }

    public function destroy(DivisionStructure $divisionStructure)
    {
        if ($divisionStructure->main_photo) Storage::disk('public')->delete($divisionStructure->main_photo);
        
        if ($divisionStructure->pdf_documents) {
            foreach ($divisionStructure->pdf_documents as $pdf) Storage::disk('public')->delete($pdf['path']);
        }

        $divisionStructure->delete();
        return back()->with('success', 'Structure deleted successfully.');
    }
}