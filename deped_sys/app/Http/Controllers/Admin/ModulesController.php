<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModulesController extends Controller {
    
    public function index(Request $request) {
        $query = Modules::query();

        // 1. Search Filter (Title & Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Sort Filter
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
            $query->latest('created_at')->latest('id'); // Default Sort
        }

        // withQueryString() ensures filters stay active when paginating
        $modules = $query->paginate(10)->withQueryString();

        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request) {
        $request->validate([
            // Added unique validation to prevent duplicate module titles
            'title' => 'required|string|max:255|unique:k12_modules,title',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('file');
        // Appended time() to prevent file overwrites
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('modules/files', $fileName, 'public');
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
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
            // Ignore the current record's ID to allow updating the same module
            'title' => 'required|string|max:255|unique:k12_modules,title,' . $id,
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,csv,xls,xlsx|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle explicitly removing the file
        if ($request->remove_file == '1') {
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }
            $data['file_path'] = null;
            $data['file_type'] = null;
        }

        // Handle explicitly removing the image
        if ($request->remove_image == '1') {
            if ($module->image_path) {
                Storage::disk('public')->delete($module->image_path);
            }
            $data['image_path'] = null;
        }
        
        if ($request->hasFile('file')) {
            if($module->file_path) Storage::disk('public')->delete($module->file_path);
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $data['file_path'] = $file->storeAs('modules/files', $fileName, 'public');
            $data['file_type'] = $file->getClientOriginalExtension();
        }
        
        if ($request->hasFile('image')) {
            if($module->image_path) Storage::disk('public')->delete($module->image_path);
            
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $data['image_path'] = $imageFile->storeAs('modules/images', $imageName, 'public');
        }

        $module->update($data);
        
        return back()->with('success', 'Module updated successfully!');
    }

    public function destroy($id) {
        $module = Modules::findOrFail($id);

        $filesToDelete = array_filter([$module->file_path, $module->image_path]);
        if (!empty($filesToDelete)) {
            Storage::disk('public')->delete($filesToDelete);
        }
        
        $module->delete();
        
        return back()->with('success', 'Module deleted successfully!');
    }
}