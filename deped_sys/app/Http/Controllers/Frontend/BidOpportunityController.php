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
    public function index($category)
    {
        // FILTER: Only get items for THIS specific category!
        $opportunities = BidOpportunity::where('category', $category)
                                       ->latest()
                                       ->paginate(5);
        
        return view('list', [
            'type_name' => $this->getCategoryTitle($category),
            'type_path' => 'procurement/' . $category, 
            'items' => $opportunities,
            'category' => $category // passing this just in case your view needs it
        ]);
    }

    // Public detail/"Read More" page
    public function show($category, $id)
    {
        // FILTER: Ensure the item actually belongs to this category
        $opportunity = BidOpportunity::where('category', $category)->findOrFail($id);
        
        return view('read_more', [
            'type_name' => $this->getCategoryTitle($category),
            'type_path' => 'procurement/' . $category, 
            'item' => $opportunity,
            'category' => $category
        ]);
    }
}