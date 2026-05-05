<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisory; 
use Illuminate\Support\Facades\Storage;

class AdvisoryController extends Controller
{
    public function store(Request $request)
    {
        $messages = [
            'title.unique' => 'This Advisory Title already exists. Please provide a unique title.',
            'image.max' => 'The banner image must not exceed 2MB.',
            'pdf.max' => 'The PDF document must not exceed 10MB.',
        ];

        $request->validate([
            'title' => 'required|string|max:255|unique:advisories,title',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'required|mimes:pdf|max:10000',
        ], $messages);

        $imageFile = $request->file('image');
        $imageFilename = time() . '_' . $imageFile->getClientOriginalName();
        $imagePath = $imageFile->storeAs('advisories/images', $imageFilename, 'public');

        $pdfFile = $request->file('pdf');
        $pdfFilename = time() . '_' . $pdfFile->getClientOriginalName();
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

        $advisories = $query->latest()->paginate(10); // Increased to 10 for admin visibility

        return view('admin.advisories.index', compact('advisories'));
    }

    public function update(Request $request, $id)
    {
        $advisory = Advisory::findOrFail($id);

        $messages = [
            'title.unique' => 'This Advisory Title already exists.',
        ];

        $request->validate([
            'title' => 'required|string|max:255|unique:advisories,title,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10000',
        ], $messages);

        $advisory->title = $request->title;

        if ($request->hasFile('image')) {
            if ($advisory->image_path) Storage::disk('public')->delete($advisory->image_path);
            $imgFile = $request->file('image');
            $imgFilename = time() . '_' . $imgFile->getClientOriginalName();
            $advisory->image_path = $imgFile->storeAs('advisories/images', $imgFilename, 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($advisory->pdf_path) Storage::disk('public')->delete($advisory->pdf_path);
            $pdfFile = $request->file('pdf');
            $pdfFilename = time() . '_' . $pdfFile->getClientOriginalName();
            $advisory->pdf_path = $pdfFile->storeAs('advisories/pdfs', $pdfFilename, 'public');
        }

        $advisory->save();

        return back()->with('success', 'Advisory updated successfully!');
    }

    public function destroy($id)
    {
        $advisory = Advisory::findOrFail($id);
        Storage::disk('public')->delete([$advisory->image_path, $advisory->pdf_path]);
        $advisory->delete();
        return back()->with('success', 'Advisory deleted successfully!');
    }
}