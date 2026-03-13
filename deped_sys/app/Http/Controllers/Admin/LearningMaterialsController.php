<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LearningMaterialsController extends Controller
{
    public function index()
    {
        return view('admin.learning_materials.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Pass the raw query builder directly to DataTables
            $data = LearningMaterials::query();
            
            return DataTables::eloquent($data)
                ->addColumn('action', function ($row) {
                    $title = htmlspecialchars($row->title ?? '', ENT_QUOTES, 'UTF-8');
                    $desc = htmlspecialchars($row->description ?? '', ENT_QUOTES, 'UTF-8');
                    
                    $editBtn = '<button type="button" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors edit-material mr-2" data-id="'.$row->id.'" data-title="'.$title.'" data-description="'.$desc.'" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>';
                    
                    $deleteBtn = '<button type="button" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors delete-material" data-id="'.$row->id.'" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                    
                    return '<div class="flex justify-center">'.$editBtn.$deleteBtn.'</div>';
                })
                ->editColumn('created_at', function ($row) {
                    // Safely format the date to prevent Carbon trailing data errors
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
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
            Storage::disk('public')->delete($material->file_path);

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
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return response()->json(['success' => 'Learning material deleted successfully!']);
    }
}