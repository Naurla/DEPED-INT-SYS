<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningMaterialsController extends Controller
{
    public function index()
    {
        $materials = LearningMaterials::latest()->paginate(10);
        return view('admin.learning_materials.index', compact('materials'));
    }

   public function store(Request $request) {
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        // Support all major classroom formats
        'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
    ]);

    $file = $request->file('file');
    $fileName = $file->getClientOriginalName();
    $filePath = $file->storeAs('learning_materials/files', $fileName, 'public');

    \App\Models\LearningMaterials::create([
        'title' => $request->title,
        'description' => $request->description,
        'file_path' => $filePath,
        'file_type' => $file->getClientOriginalExtension(),
    ]);

    return back()->with('success', 'Material uploaded successfully!');
}

    public function update(Request $request, string $id)
    {
        $material = LearningMaterials::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // UPDATED: Added csv, xls, xlsx
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,csv,xls,xlsx|max:20480',
        ]);

        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $dataToUpdate['file_path'] = $file->storeAs('learning_materials', $fileName, 'public');
            $dataToUpdate['file_type'] = $file->getClientOriginalExtension();
        }

        $material->update($dataToUpdate);

        return back()->with('success', 'Learning material updated successfully!');
    }

    public function destroy(string $id)
    {
        $material = LearningMaterials::findOrFail($id);
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return back()->with('success', 'Learning material deleted successfully!');
    }
}