<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SeniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class SeniorHighFrontendController extends Controller
{
    public function index(Request $request)
    {
        $contents = SeniorHighContent::orderBy('id', 'asc')->get();
        
        foreach ($contents as $item) {
            $csvData = []; 
            
            if ($item->csv_path && Storage::disk('public')->exists($item->csv_path)) {
                $filePath = Storage::disk('public')->path($item->csv_path);
                
                $file = fopen($filePath, 'r');
                if ($file !== false) {
                    while (($data = fgetcsv($file, 1000, ",")) !== false) {
                        $csvData[] = $data;
                    }
                    fclose($file);
                }
            }
            
            // Paginate the CSV rows
            if (!empty($csvData)) {
                $header = array_shift($csvData);
                
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage('page_' . $item->id);
                $currentItems = array_slice($csvData, ($currentPage - 1) * $perPage, $perPage);
                
                $paginator = new LengthAwarePaginator(
                    $currentItems, 
                    count($csvData), 
                    $perPage, 
                    $currentPage, 
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'pageName' => 'page_' . $item->id
                    ]
                );

                $item->tableHeader = $header;
                $item->tableData = $paginator;
            } else {
                $item->tableHeader = [];
                $item->tableData = null;
            }
        }
        
        return view('frontend.senior_high.index', compact('contents'));
    }
}