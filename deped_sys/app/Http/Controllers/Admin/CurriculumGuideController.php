<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumGuide;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurriculumGuideController extends Controller
{
    public function index() {
    $pageData = CurriculumPage::first();
    $strands = LearningStrand::with('materials')->get();
    $guides = CurriculumGuide::all(); // ADD THIS LINE

    // Pass $guides to the view
    return view('admin.curriculum.index', compact('pageData', 'strands', 'guides'));
}

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = CurriculumGuide::query();
            
            return DataTables::eloquent($data)
                ->addColumn('action', function ($row) {
                    $title = htmlspecialchars($row->title ?? '', ENT_QUOTES, 'UTF-8');
                    $link = htmlspecialchars($row->link ?? '', ENT_QUOTES, 'UTF-8');
                    
                    $editBtn = '<button type="button" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors edit-guide mr-2" data-id="'.$row->id.'" data-title="'.$title.'" data-link="'.$link.'" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>';
                    
                    $deleteBtn = '<button type="button" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors delete-guide" data-id="'.$row->id.'" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
                    
                    return '<div class="flex justify-center">'.$editBtn.$deleteBtn.'</div>';
                })
                ->addColumn('link_preview', function($row) {
                    return '<a href="'.$row->link.'" target="_blank" class="text-blue-600 hover:underline text-xs">'.$row->link.'</a>';
                })
                ->rawColumns(['action', 'link_preview'])
                ->make(true);
        }
    }

   public function storeGuide(Request $request) {
    $request->validate(['title' => 'required', 'link' => 'required|url']);
    CurriculumGuide::create($request->all());
    return back()->with('success', 'Curriculum Guide added!');
}

public function updateGuide(Request $request, CurriculumGuide $guide) {
    $request->validate(['title' => 'required', 'link' => 'required|url']);
    $guide->update($request->all());
    return back()->with('success', 'Curriculum Guide updated!');
}

public function destroyGuide(CurriculumGuide $guide) {
    $guide->delete();
    return back()->with('success', 'Curriculum Guide deleted!');
}
}