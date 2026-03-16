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
        // Fetch materials with standard Laravel pagination (10 per page)
        $materials = LearningMaterials::latest()->paginate(10);
        
        return view('admin.learning_materials.index', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,ppt,pptx,doc,docx|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('learning_materials', $fileName, 'public');
            $fileType = $file->getClientOriginalExtension();

            LearningMaterials::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_type' => $fileType,
            ]);

            return response()->json(['success' => 'Learning material uploaded successfully!']);
        }

        return response()->json(['error' => 'Please upload a file.'], 400);
    }

    public function update(Request $request, string $id)
    {
        $material = LearningMaterials::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx|max:20480',
        ]);

        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('learning_materials', $fileName, 'public');
            $dataToUpdate['file_path'] = $filePath;
            $dataToUpdate['file_type'] = $file->getClientOriginalExtension();
        }

        $material->update($dataToUpdate);

        return response()->json(['success' => 'Learning material updated successfully!']);
    }

    public function destroy(string $id)
    {
        $material = LearningMaterials::findOrFail($id);
        
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();

        // CHANGED: Standard Laravel redirect instead of JSON response
        return redirect()->route('admin.learning-materials.index')
                         ->with('success', 'Learning material deleted successfully!');
    }
}