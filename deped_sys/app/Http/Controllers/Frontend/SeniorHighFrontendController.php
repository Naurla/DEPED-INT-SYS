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
        $publicContents = SeniorHighContent::where('school_type', 'public')
                            ->orderBy('id', 'asc')
                            ->get()
                            ->map(fn($item) => $this->parseCsvData($item, 'SHS'));

        $privateContents = SeniorHighContent::where('school_type', 'private')
                            ->orderBy('id', 'asc')
                            ->get()
                            ->map(fn($item) => $this->parseCsvData($item, 'SHS'));
        
        return view('frontend.senior_high.index', compact('publicContents', 'privateContents'));
    }

    private function parseCsvData($item, $levelKeyword)
    {
        $filteredRows = []; 
        $header = [];
        $headerFound = false;
        $expectedSector = strtolower($item->school_type); 

        if ($item->csv_path && Storage::disk('public')->exists($item->csv_path)) {
            $path = Storage::disk('public')->path($item->csv_path);
            if (($file = fopen($path, 'r')) !== FALSE) {
                while (($line = fgetcsv($file)) !== FALSE) {
                    if (!$line || empty(array_filter($line))) continue;
                    if (!$headerFound) {
                        $col0 = isset($line[0]) ? trim($line[0]) : '';
                        $col1 = isset($line[1]) ? trim($line[1]) : '';
                        if (stripos($col0, 'District') !== false || stripos($col1, 'beis_school_id') !== false) {
                            $header = $line;
                            $headerFound = true;
                        }
                        continue;
                    }
                    if (count($line) >= 7) {
                        $sector = strtolower(trim($line[4])); 
                        $classification = strtoupper(trim($line[6])); 
                        $matchesLevel = str_contains($classification, $levelKeyword) || str_contains($classification, 'ALL OFFERING');
                        if ($sector === $expectedSector && $matchesLevel) {
                            $filteredRows[] = $line;
                        }
                    }
                }
                fclose($file);
            }
        }
        
        if (!empty($filteredRows) && !empty($header)) {
            $perPage = 10;
            $currentPage = LengthAwarePaginator::resolveCurrentPage('page_' . $item->id); 
            $currentItems = array_slice($filteredRows, ($currentPage - 1) * $perPage, $perPage);
            $item->tableData = new LengthAwarePaginator($currentItems, count($filteredRows), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page_' . $item->id
            ]);
            $item->tableHeader = $header; 
        } else {
            $item->tableHeader = [];
            $item->tableData = null;
        }
        return $item;
    }
}