<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisory;

class IssuanceController extends Controller
{
    public function index()
    {
        // Fixes pagination error by using paginate()
        // Unique pageNames ('adv_page', etc.) allow independent tab pagination
        $advisories = Advisory::where('type', 'advisory')->latest()->paginate(10, ['*'], 'adv_page');
        $memoranda = Advisory::where('type', 'memorandum')->latest()->paginate(10, ['*'], 'mem_page');
        $hrmpsb = Advisory::where('type', 'hrmpsb')->latest()->paginate(10, ['*'], 'hr_page');

        return view('issuances.index', compact('advisories', 'memoranda', 'hrmpsb'));
    }
}