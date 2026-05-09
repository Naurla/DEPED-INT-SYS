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

        // Fetch all matching results (using get() since FAQs are an accordion list, not paginated pages)
        $faqs = $query->latest('created_at')->get();

        return view('curriculum.faq', compact('faqs'));
    }
}