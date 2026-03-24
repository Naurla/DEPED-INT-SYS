<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JuniorHighContent;
use Illuminate\Support\Facades\Storage;

class JuniorHighFrontendController extends Controller
{
    public function index() 
    {
        $contents = JuniorHighContent::all()->map(function ($item) {
            // 1. Create a temporary local array
            $tempTable = []; 

            if ($item->csv_path && Storage::disk('public')->exists($item->csv_path)) {
                $path = Storage::disk('public')->path($item->csv_path);
                
                if (($file = fopen($path, 'r')) !== FALSE) {
                    while (($line = fgetcsv($file)) !== FALSE) {
                        // 2. Push data into the local array
                        $tempTable[] = $line; 
                    }
                    fclose($file);
                }
            }
            
            // 3. Assign the entire array to the model property at once
            $item->tableData = $tempTable; 
            
            return $item;
        });

        return view('frontend.junior_high.index', compact('contents'));
    }
}