<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningMaterialsController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningMaterials::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Year Filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Sort Filter
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('created_at')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('title', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('title', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('created_at')->latest('id');
                    break;
            }
        } else {
            $query->latest('created_at')->latest('id');
        }

        $materials = $query->paginate(10)->withQueryString();

        // Get unique years for the dropdown
        $years = LearningMaterials::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.learning_materials.index', compact('materials', 'years'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:learning_materials,title',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('learning_materials/files', $fileName, 'public');

        LearningMaterials::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Learning material uploaded successfully!');
    }

    public function update(Request $request, string $id)
    {
        $material = LearningMaterials::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255|unique:learning_materials,title,' . $id,
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,csv,xls,xlsx|max:20480',
        ]);

        $dataToUpdate = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->remove_file == '1') {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $dataToUpdate['file_path'] = null;
            $dataToUpdate['file_type'] = null;
        }

        if ($request->hasFile('file')) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dataToUpdate['file_path'] = $file->storeAs('learning_materials/files', $fileName, 'public');
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