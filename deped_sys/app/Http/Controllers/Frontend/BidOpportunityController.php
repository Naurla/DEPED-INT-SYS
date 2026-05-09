<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;

class BidOpportunityController extends Controller
{
    // Helper to format the category name nicely for the View
    private function getCategoryTitle($category)
    {
        $titles = [
            'bid-opportunities' => 'Bid Opportunities',
            'apcpi' => 'Agency Procurement Compliance and Performance Indicators',
            'app-cse' => 'Annual Procurement Plan – Common User Supplies',
            'app-non-cse' => 'Annual Procurement Plan – Non CSE',
            'award-notices' => 'Award Notices',
            'pmr' => 'Procurement Monitoring Report',
            'pre-bid-minutes' => 'Minutes of Pre-Bid Conference',
        ];
        return $titles[$category] ?? 'Procurement Documents';
    }

    // Public page listing opportunities by dynamic category
    public function index(Request $request, $category)
    {
        $query = BidOpportunity::where('category', $category);

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        // Filter by Keyword (Title or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginate and retain search filters in the URL 
        // Note: I increased pagination to 10 (from 5) to match issuances, but you can change it back if you prefer!
        $opportunities = $query->latest('date')->latest('id')->paginate(10)->withQueryString();
        
        return view('/procurement/list', [
            'type_name' => $this->getCategoryTitle($category),
            'type_path' => 'procurement/' . $category, 
            'items' => $opportunities,
            'category' => $category 
        ]);
    }

    // Public detail/"Read More" page
    public function show($category, $id)
    {
        // FILTER: Ensure the item actually belongs to this category
        $opportunity = BidOpportunity::where('category', $category)->findOrFail($id);
        
        return view('/procurement/read_more', [
            'type_name' => $this->getCategoryTitle($category),
            'type_path' => 'procurement/' . $category, 
            'item' => $opportunity,
            'category' => $category
        ]);
    }
}