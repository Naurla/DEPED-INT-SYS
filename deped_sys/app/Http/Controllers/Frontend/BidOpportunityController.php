<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;

class BidOpportunityController extends Controller
{
    // Public page listing all bid opportunities
    public function index()
    {
        // Get the latest bid opportunities with pagination
        $opportunities = BidOpportunity::latest()->paginate(10);
        
        return view('list', [
            'type_name' => 'Bid Opportunities',
            'type_path' => 'bid-opportunities',
            'items' => $opportunities,
        ]);
    }

    // Public detail/"Read More" page
    public function show($id)
    {
        $opportunity = BidOpportunity::findOrFail($id);
        
        return view('read_more', [
            'type_name' => 'Bid Opportunities',
            'type_path' => 'bid-opportunities',
            'item' => $opportunity,
        ]);
    }
}