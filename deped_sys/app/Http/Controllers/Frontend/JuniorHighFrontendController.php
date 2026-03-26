<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JuniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class JuniorHighFrontendController extends Controller
{
    public function index(Request $request) 
    {
        $contents = JuniorHighContent::all()->map(function ($item) use ($request) {
            $tempTable = []; 

            if ($item->csv_path && Storage::disk('public')->exists($item->csv_path)) {
                $path = Storage::disk('public')->path($item->csv_path);
                
                if (($file = fopen($path, 'r')) !== FALSE) {
                    while (($line = fgetcsv($file)) !== FALSE) {
                        $tempTable[] = $line; 
                    }
                    fclose($file);
                }
            }
            
            // Paginate the CSV rows
            if (!empty($tempTable)) {
                // 1. Separate the header (first row) from the data
                $header = array_shift($tempTable);
                
                // 2. Setup array pagination (10 rows per page)
                $perPage = 10;
                // Use a unique page name for each table (e.g., ?page_1=2)
                $currentPage = LengthAwarePaginator::resolveCurrentPage('page_' . $item->id); 
                $currentItems = array_slice($tempTable, ($currentPage - 1) * $perPage, $perPage);
                
                $paginator = new LengthAwarePaginator(
                    $currentItems, 
                    count($tempTable), 
                    $perPage, 
                    $currentPage, 
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'pageName' => 'page_' . $item->id
                    ]
                );

                $item->tableHeader = $header; // Store header separately
                $item->tableData = $paginator; // Store paginated rows
            } else {
                $item->tableHeader = [];
                $item->tableData = null;
            }
            
            return $item;
        });

        return view('frontend.junior_high.index', compact('contents'));
    }
}