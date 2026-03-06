<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    public function advisories()
    {
        // Make sure 'advisory' matches the exact spelling in your database 'type' column
        $items = Issuance::where('type', 'advisory')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Advisories',
            'color' => 'red',
           
        ]);
    }

    public function memoranda()
    {
        $items = Issuance::where('type', 'memorandum')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Memoranda',
            'color' => 'blue',
           
        ]);
    }

    public function hrmpsb()
    {
        $items = Issuance::where('type', 'hrmpsb')->latest()->paginate(10);
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'HRMPSB Assessment Results',
            'color' => 'yellow',
          
        ]);
    }
}