<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModulesController extends Controller {
    
    public function index() {
        $modules = Modules::latest()->paginate(10);
        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // UPDATED: Added csv, xls, xlsx to mimes
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('modules/files', $fileName, 'public');
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = $imageFile->getClientOriginalName();
            $imagePath = $imageFile->storeAs('modules/images', $imageName, 'public');
        }

        Modules::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'Module uploaded successfully!');
    }

    public function update(Request $request, $id) {
        $module = Modules::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // UPDATED: Added validation for the file during update as well
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        if ($request->hasFile('file')) {
            if($module->file_path) Storage::disk('public')->delete($module->file_path);
            
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('modules/files', $fileName, 'public');
            $data['file_type'] = $file->getClientOriginalExtension();
        }
        
        if ($request->hasFile('image')) {
            if($module->image_path) Storage::disk('public')->delete($module->image_path);
            
            $imageFile = $request->file('image');
            $imageName = $imageFile->getClientOriginalName();
            $data['image_path'] = $imageFile->storeAs('modules/images', $imageName, 'public');
        }

        $module->update($data);
        
        return back()->with('success', 'Module updated!');
    }

    public function destroy($id) {
        $module = Modules::findOrFail($id);

        $filesToDelete = array_filter([$module->file_path, $module->image_path]);
        if (!empty($filesToDelete)) {
            Storage::disk('public')->delete($filesToDelete);
        }
        
        $module->delete();
        
        return redirect()->route('admin.modules.index')
                         ->with('success', 'Module deleted successfully!');
    }
}