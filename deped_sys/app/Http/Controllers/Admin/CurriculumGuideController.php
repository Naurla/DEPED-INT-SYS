<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumGuide;
use App\Models\CurriculumPage; 
use App\Models\LearningStrand; 
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurriculumGuideController extends Controller
{
    public function index(Request $request) {
        $pageData = CurriculumPage::firstOrCreate([]);
        
        // --- 1. Learning Strands Query with Filters ---
        $strandQuery = LearningStrand::with('materials');
        
        if ($request->filled('strand_search')) {
            $search = $request->strand_search;
            $strandQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('strand_sort')) {
            if ($request->strand_sort === 'a_z') $strandQuery->orderBy('name', 'asc');
            elseif ($request->strand_sort === 'z_a') $strandQuery->orderBy('name', 'desc');
            elseif ($request->strand_sort === 'newest') $strandQuery->latest('created_at');
            elseif ($request->strand_sort === 'oldest') $strandQuery->oldest('created_at');
        } else {
            $strandQuery->orderBy('sort_order');
        }
        
        $strands = $strandQuery->get();

        // --- 2. Curriculum Guides Query with Filters ---
        $guideQuery = CurriculumGuide::query();
        
        if ($request->filled('guide_search')) {
            $search = $request->guide_search;
            $guideQuery->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('guide_sort')) {
            if ($request->guide_sort === 'a_z') $guideQuery->orderBy('title', 'asc');
            elseif ($request->guide_sort === 'z_a') $guideQuery->orderBy('title', 'desc');
            elseif ($request->guide_sort === 'oldest') $guideQuery->oldest('created_at');
            elseif ($request->guide_sort === 'newest') $guideQuery->latest('created_at');
        } else {
            $guideQuery->latest('created_at');
        }

        $guides = $guideQuery->get();

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
        $request->validate([
            'title' => 'required|string|max:255|unique:curriculum_guides,title',
            'link' => 'required|url'
        ], [
            'title.unique' => 'A curriculum guide with this title already exists. Please provide a unique title.'
        ]);

        CurriculumGuide::create($request->all());
        return back()->with('success', 'Curriculum Guide added!');
    }

    public function updateGuide(Request $request, CurriculumGuide $guide) {
        $request->validate([
            'title' => 'required|string|max:255|unique:curriculum_guides,title,' . $guide->id,
            'link' => 'required|url'
        ], [
            'title.unique' => 'A curriculum guide with this title already exists. Please provide a unique title.'
        ]);

        $guide->update($request->all());
        return back()->with('success', 'Curriculum Guide updated!');
    }

    public function destroyGuide(CurriculumGuide $guide) {
        $guide->delete();
        return back()->with('success', 'Curriculum Guide deleted!');
    }
}