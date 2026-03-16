<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModulesController extends Controller {
    
    public function index() {
        // Fetch modules with standard Laravel pagination (10 per page)
        $modules = Modules::latest()->paginate(10);
        
        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $filePath = $request->file('file')->store('modules/files', 'public');
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('modules/images', 'public') : null;

        Modules::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'image_path' => $imagePath,
        ]);

        return response()->json(['success' => 'Module uploaded successfully!']);
    }

    public function update(Request $request, Modules $module) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        
        if ($request->hasFile('file')) {
            if($module->file_path) Storage::disk('public')->delete($module->file_path);
            $data['file_path'] = $request->file('file')->store('modules/files', 'public');
            $data['file_type'] = $request->file('file')->getClientOriginalExtension();
        }
        
        if ($request->hasFile('image')) {
            if($module->image_path) Storage::disk('public')->delete($module->image_path);
            $data['image_path'] = $request->file('image')->store('modules/images', 'public');
        }

        $module->update($data);
        return response()->json(['success' => 'Module updated!']);
    }

    public function destroy(Modules $module) {
        // Use array_filter to avoid passing null paths to Storage::delete
        $filesToDelete = array_filter([$module->file_path, $module->image_path]);
        if (!empty($filesToDelete)) {
            Storage::disk('public')->delete($filesToDelete);
        }
        
        $module->delete();
        
        // Standard Laravel redirect instead of JSON response
        return redirect()->route('admin.modules.index')
                         ->with('success', 'Module deleted successfully!');
    }
}