<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssuanceController extends Controller
{
    public function advisories(Request $request)
    {
        $query = Issuance::where('type', 'advisory');

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        // Filter by Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest('date')->paginate(10)->withQueryString();
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest('date')->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest('date')->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Advisories',
            'color' => 'red',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function memoranda(Request $request)
    {
        $query = Issuance::where('type', 'memorandum');

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest('date')->paginate(10)->withQueryString();
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest('date')->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest('date')->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Memoranda',
            'color' => 'blue',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function hrmpsb(Request $request)
    {
        $query = Issuance::where('type', 'hrmpsb');

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest('date')->paginate(10)->withQueryString();
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest('date')->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest('date')->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'HRMPSB Assessment Results',
            'color' => 'yellow',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function show($id)
    {
        $issuance = Issuance::findOrFail($id);
        $recentAdvisories = Issuance::where('type', 'advisory')
                                    ->where('id', '!=', $issuance->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        $recentMemoranda = Issuance::where('type', 'memorandum')
                                   ->where('id', '!=', $issuance->id)
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('issuances.show', compact('issuance', 'recentAdvisories', 'recentMemoranda'));
    }

    public function adminIndex(Request $request)
    {
        $type = $request->query('type', 'advisory'); 
        $query = Issuance::where('type', $type);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // NEW: Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('date')->oldest('id');
                    break;
                case 'a_z':
                    $query->orderBy('title', 'asc');
                    break;
                case 'z_a':
                    $query->orderBy('title', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('date')->latest('id');
                    break;
            }
        } else {
            $query->latest('date')->latest('id');
        }

        $issuances = $query->paginate(10)->withQueryString();

        $years = Issuance::where('type', $type)
            ->whereNotNull('date')
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.issuances.index', compact('issuances', 'type', 'years'));
    }

    public function index(Request $request)
    {
        $query = Issuance::query();

        // 1. Filter by Type (Advisory, Memo, HRMPSB)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 2. Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // 3. Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        // 4. Filter by Keyword (Title or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginate and retain filter URLs for the page buttons
        $items = $query->latest('date')->latest('id')->paginate(10)->withQueryString();

        return view('issuances.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:issuances,title',
            'description' => 'nullable|string', 
            'type' => 'required|in:advisory,memorandum,hrmpsb',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'link' => 'nullable|url|max:2000',
            'date' => 'nullable|date',
        ]);

        $path = null;
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = time() . '_' . $pdfFile->getClientOriginalName();
            $path = $pdfFile->storeAs('issuances/' . $validated['type'], $pdfFilename, 'public');
        }
        
        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'pdf_path' => $path,
            'link' => $validated['link'] ?? null,
            'date' => $validated['date'] ?: now()->toDateString(),
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' uploaded successfully!');
    }

    public function update(Request $request, $id)
    {
        $issuance = Issuance::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:issuances,title,' . $id,
            'description' => 'nullable|string', 
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'link' => 'nullable|url|max:2000', 
            'date' => 'nullable|date',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'link' => $validated['link'] ?? null,
            'date' => $validated['date'] ?: now()->toDateString(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($issuance->pdf_path) {
                Storage::disk('public')->delete($issuance->pdf_path);
            }
            $pdfFile = $request->file('pdf_file');
            $pdfFilename = time() . '_' . $pdfFile->getClientOriginalName();
            $dataToUpdate['pdf_path'] = $pdfFile->storeAs('issuances/' . $issuance->type, $pdfFilename, 'public');
        }

        if ($request->input('remove_pdf') == '1' && $issuance->pdf_path) {
            Storage::disk('public')->delete($issuance->pdf_path);
            $dataToUpdate['pdf_path'] = null;
        }

        $issuance->update($dataToUpdate);

        return back()->with('success', 'Issuance updated successfully!');
    }

    public function destroy($id)
    {
        $issuance = Issuance::findOrFail($id);
        if ($issuance->pdf_path) {
            Storage::disk('public')->delete($issuance->pdf_path);
        }
        $issuance->delete();
        return back()->with('success', 'Issuance deleted successfully!');
    }

    public function globalSearch(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            $results = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, ['path' => $request->url()]);
            return view('frontend.search_results', compact('results', 'query'));
        }

        // 1. Search Issuances (Advisories, Memoranda, HRMPSB)
        $issuances = \App\Models\Issuance::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null, // Description removed from output
                    'type' => $item->type ?? 'Issuance',
                    'date' => $item->created_at,
                    'url' => route('issuances.show', $item->id),
                ];
            });

        // 2. Search Dynamic Pages (WITH INFINITE DEEP ANCESTOR VISIBILITY FIX)
        $pages = \App\Models\Page::with('parent') // Eager load parent to prevent excessive DB queries
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->get()
            ->filter(function ($page) {
                // Traverse up the hierarchy tree to ensure the page AND ALL ancestors are visible
                $current = $page;
                while ($current) {
                    if ($current->show_in_nav != 1) {
                        return false; // If this page OR any parent is disabled, completely hide it
                    }
                    $current = $current->parent; // Move up to the next parent
                }
                return true; // All ancestors are good!
            })
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null, // Description removed from output
                    'type' => $item->title, // Outputs the actual page name
                    'date' => $item->created_at,
                    'url' => route('frontend.page', $item->slug),
                ];
            });

        // 3. Search Bid Opportunities (Procurement)
        $bids = \App\Models\BidOpportunity::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null, // Description removed from output
                    'type' => 'Procurement',
                    'date' => $item->created_at,
                    'url' => route('procurement.show', ['category' => $item->category, 'id' => $item->id]),
                ];
            });

        // 4. Search Enrollment Statistics
        $enrollmentStats = \App\Models\EnrollmentStatistic::where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null,
                    'type' => 'Enrollment Statistic',
                    'date' => $item->created_at,
                    'url' => route('enrollment-statistics.show', $item->id),
                ];
            });

        // 5. Search Modules
        $modules = \App\Models\Modules::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null,
                    'type' => 'Module',
                    'date' => $item->created_at,
                    'url' => route('k12.als.modules.show', $item->id),
                ];
            });

        // 6. Search ALS Stories
        $alsStories = \App\Models\AlsStory::where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null,
                    'type' => 'ALS Story',
                    'date' => $item->created_at,
                    'url' => route('als-stories.show', $item->id),
                ];
            });

        // 7. Search ALS Implementers
        $alsImplementers = \App\Models\AlsImplementer::where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => null,
                    'type' => 'ALS Implementer',
                    'date' => $item->created_at,
                    'url' => route('als-implementers.show', $item->id),
                ];
            });

        // Combine all results into a single collection and sort by newest
        $allResults = $issuances
            ->concat($pages)
            ->concat($bids)
            ->concat($enrollmentStats)
            ->concat($modules)
            ->concat($alsStories)
            ->concat($alsImplementers)
            ->sortByDesc('date');

        // Manually paginate the combined collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $paginatedResults = new \Illuminate\Pagination\LengthAwarePaginator(
            $allResults->forPage($page, $perPage)->values(),
            $allResults->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );  

        $results = $paginatedResults;

        return view('frontend.search_results', compact('results', 'query'));
    }


}