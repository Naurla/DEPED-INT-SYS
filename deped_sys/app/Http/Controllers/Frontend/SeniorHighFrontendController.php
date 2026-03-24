<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SeniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeniorHighFrontendController extends Controller
{
    public function index()
    {
        // Fetch all senior high contents
        $contents = SeniorHighContent::orderBy('id', 'asc')->get();
        
        // Loop through each content and parse the CSV if it exists
        foreach ($contents as $item) {
            // 1. Create a temporary local array
            $csvData = []; 
            
            if ($item->csv_path && Storage::disk('public')->exists($item->csv_path)) {
                $filePath = Storage::disk('public')->path($item->csv_path);
                
                // Read CSV file
                $file = fopen($filePath, 'r');
                if ($file !== false) {
                    while (($data = fgetcsv($file, 1000, ",")) !== false) {
                        // 2. Push data to the temporary array instead of the model property
                        $csvData[] = $data;
                    }
                    fclose($file);
                }
            }
            
            // 3. Assign the fully built array to the model property all at once
            $item->tableData = $csvData;
        }
        
        return view('frontend.senior_high.index', compact('contents'));
    }
}