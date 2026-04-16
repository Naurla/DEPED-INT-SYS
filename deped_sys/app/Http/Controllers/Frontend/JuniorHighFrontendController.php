<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JuniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class JuniorHighFrontendController extends Controller
{
    public function index(Request $request) 
    {
        $publicContents = JuniorHighContent::where('school_type', 'public')
                            ->latest()
                            ->get()
                            ->map(fn($item) => $this->parseSpreadsheetData($item, 'JHS'));

        $privateContents = JuniorHighContent::where('school_type', 'private')
                            ->latest()
                            ->get()
                            ->map(fn($item) => $this->parseSpreadsheetData($item, 'JHS'));

        return view('frontend.junior_high.index', compact('publicContents', 'privateContents'));
    }

    private function parseSpreadsheetData($item, $levelKeyword)
    {
        $filteredRows = []; 
        $header = [];
        $item->tableData = null;
        $item->tableHeader = [];

        if ($item->csv_path) {
            $extension = strtolower(pathinfo($item->csv_path, PATHINFO_EXTENSION));
            
            // Support CSV and Excel files
            if (in_array($extension, ['csv', 'xls', 'xlsx']) && Storage::disk('public')->exists($item->csv_path)) {
                $path = Storage::disk('public')->path($item->csv_path);
                
                try {
                    // Load the file (handles both CSV and Excel automatically)
                    $spreadsheet = IOFactory::load($path);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $allData = $worksheet->toArray(); // Convert spreadsheet to array

                    $headerFound = false;
                    $expectedSector = strtolower($item->school_type); 
                    $allRowsFallback = [];
                    $fallbackHeader = [];
                    
                    $rowIndex = 0;
                    foreach ($allData as $line) {
                        // Skip completely empty rows
                        if (!$line || empty(array_filter($line))) continue;

                        if ($rowIndex === 0) {
                            $fallbackHeader = $line;
                        } else {
                            $allRowsFallback[] = $line;
                        }
                        $rowIndex++;

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
                    
                    // Fallback if strict filter found nothing
                    if (empty($filteredRows) || empty($header)) {
                        $filteredRows = $allRowsFallback;
                        $header = $fallbackHeader;
                    }
                    
                    // Paginate
                    if (!empty($filteredRows) && !empty($header)) {
                        $perPage = 10;
                        $currentPage = LengthAwarePaginator::resolveCurrentPage('page_' . $item->id); 
                        $currentItems = array_slice($filteredRows, ($currentPage - 1) * $perPage, $perPage);
                        
                        $item->tableData = new LengthAwarePaginator(
                            $currentItems, count($filteredRows), $perPage, $currentPage, 
                            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'page_' . $item->id]
                        );
                        $item->tableHeader = $header; 
                    }

                } catch (\Exception $e) {
                    // If parsing fails, it fails silently and will just show the download button
                }
            }
        }
        
        return $item;
    }
}