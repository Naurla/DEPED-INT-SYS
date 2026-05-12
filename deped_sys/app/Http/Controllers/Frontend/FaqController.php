<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        // Only show active FAQs to the public
        $query = Faq::where('is_active', true);

        // Filter by Keyword Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        // Fetch results with 5 items per page
        // withQueryString() ensures search parameters stay in the URL when changing pages
        $faqs = $query->latest('created_at')->paginate(5)->withQueryString();

        return view('curriculum.faq', compact('faqs'));
    }
}