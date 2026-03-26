<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentStatistic;
use Illuminate\Http\Request;

class EnrollmentStatisticController extends Controller
{
    public function index()
    {
        // Paginate the statistics (10 per page)
        $items = EnrollmentStatistic::latest()->paginate(5);
        $type_name = 'Enrollment Statistics';
        
        return view('frontend.enrollment_statistics.index', compact('items', 'type_name'));
    }

    public function show($id)
    {
        $item = EnrollmentStatistic::findOrFail($id);
        $type_name = 'Enrollment Statistic';
        
        return view('frontend.enrollment_statistics.show', compact('item', 'type_name'));
    }
}