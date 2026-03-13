<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ModulesController extends Controller {
    public function index() {
        return view('admin.modules.index');
    }

    public function getData(Request $request) {
        if ($request->ajax()) {
            $data = Modules::query();
            return DataTables::eloquent($data)
                ->addColumn('action', function ($row) {
                    $title = htmlspecialchars($row->title ?? '', ENT_QUOTES, 'UTF-8');
                    $desc = htmlspecialchars($row->description ?? '', ENT_QUOTES, 'UTF-8');
                    return '<div class="flex justify-center">
                        <button type="button" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded edit-module mr-2" data-id="'.$row->id.'" data-title="'.$title.'" data-description="'.$desc.'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                        <button type="button" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded delete-module" data-id="'.$row->id.'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>';
                })
                ->rawColumns(['action'])->make(true);
        }
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
        $data = $request->validate(['title' => 'required', 'description' => 'required']);
        
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($module->file_path);
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
        Storage::disk('public')->delete([$module->file_path, $module->image_path]);
        $module->delete();
        return response()->json(['success' => 'Deleted!']);
    }
}