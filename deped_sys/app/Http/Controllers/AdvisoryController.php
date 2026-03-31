<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisory; 
use Illuminate\Support\Facades\Storage;

class AdvisoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'required|mimes:pdf|max:10000',
        ]);

        $imageFile = $request->file('image');
        $imageFilename = $imageFile->getClientOriginalName();
        $imagePath = $imageFile->storeAs('advisories/images', $imageFilename, 'public');

        $pdfFile = $request->file('pdf');
        $pdfFilename = $pdfFile->getClientOriginalName();
        $pdfPath = $pdfFile->storeAs('advisories/pdfs', $pdfFilename, 'public');

        Advisory::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'pdf_path' => $pdfPath,
        ]);

        return back()->with('success', 'Advisory uploaded successfully!');
    }

    public function index(Request $request)
    {
        $query = Advisory::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('title', 'ilike', '%' . $searchTerm . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $advisories = $query->latest()->paginate(5);

        return view('admin.advisories.index', compact('advisories'));
    }

    public function destroy($id)
    {
        $advisory = Advisory::findOrFail($id);
        Storage::disk('public')->delete([$advisory->image_path, $advisory->pdf_path]);
        $advisory->delete();
        return back()->with('success', 'Advisory deleted!');
    }

    public function update(Request $request, $id)
    {
        $advisory = Advisory::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10000',
        ]);

        $advisory->title = $request->title;

        if ($request->hasFile('image')) {
            if ($advisory->image_path) Storage::disk('public')->delete($advisory->image_path);
            $imgFile = $request->file('image');
            $imgFilename = $imgFile->getClientOriginalName();
            $advisory->image_path = $imgFile->storeAs('advisories/images', $imgFilename, 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($advisory->pdf_path) Storage::disk('public')->delete($advisory->pdf_path);
            $pdfFile = $request->file('pdf');
            $pdfFilename = $pdfFile->getClientOriginalName();
            $advisory->pdf_path = $pdfFile->storeAs('advisories/pdfs', $pdfFilename, 'public');
        }

        $advisory->save();

        return back()->with('success', 'Advisory updated successfully!');
    }
}