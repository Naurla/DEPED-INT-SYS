<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SeniorHighContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SeniorHighFrontendController extends Controller
{
    public function index(Request $request)
    {
        // Get filters from URL
        $tab = $request->query('tab', 'public');
        $districtFilter = $request->query('district');
        $search = $request->query('search'); // Capture search input

        // Fetch ALL uploaded files and parse them (no DB where clause, so we can search inside the CSV)
        $allContents = SeniorHighContent::latest()->get()->map(function($item) use ($tab, $districtFilter, $search) {
            // Passing 'SHS' as the level keyword
            return $this->parseSpreadsheetData($item, 'SHS', $tab, $districtFilter, $search);
        });

        // Extract all unique districts found in the spreadsheets to populate the dropdown
        $districts = [];
        foreach ($allContents as $item) {
            if (!empty($item->availableDistricts)) {
                $districts = array_merge($districts, $item->availableDistricts);
            }
        }
        $districts = array_unique($districts);
        sort($districts);

        // Filter out files that don't match the search or the district
        $contents = $allContents->filter(function($item) use ($search, $districtFilter) {
            $hasTableRows = $item->tableData && $item->tableData->total() > 0;
            
            if (!empty($search)) {
                // Match if search is in Title, Content, OR inside the actual spreadsheet rows
                $inTitleOrContent = (stripos($item->title, $search) !== false) || (stripos($item->content, $search) !== false);
                return $inTitleOrContent || $hasTableRows;
            }

            if (!empty($districtFilter)) {
                // Match if district filter has rows
                return $hasTableRows;
            }

            // Show all files if no search or district filter is applied
            // (Files without tables like PDFs will still show)
            return true;
        });

        return view('frontend.senior_high.index', compact('contents', 'tab', 'districts', 'districtFilter', 'search'));
    }

    private function parseSpreadsheetData($item, $levelKeyword, $activeTab, $districtFilter, $search)
    {
        $filteredRows = []; 
        $header = [];
        $availableDistricts = [];
        $item->tableData = null;
        $item->tableHeader = [];
        $item->availableDistricts = [];

        if ($item->csv_path) {
            $extension = strtolower(pathinfo($item->csv_path, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['csv', 'xls', 'xlsx']) && Storage::disk('public')->exists($item->csv_path)) {
                $path = Storage::disk('public')->path($item->csv_path);
                
                try {
                    $spreadsheet = IOFactory::load($path);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $allData = $worksheet->toArray();

                    $headerFound = false;
                    $districtColIdx = -1;
                    $sectorColIdx = 4; // Default index if not found dynamically
                    $classColIdx = 6;  // Default classification index
                    
                    foreach ($allData as $line) {
                        if (!$line || empty(array_filter($line))) continue;

                        if (!$headerFound) {
                            $col0 = isset($line[0]) ? trim($line[0]) : '';
                            $col1 = isset($line[1]) ? trim($line[1]) : '';
                            
                            // Detect Header Row
                            if (stripos($col0, 'District') !== false || stripos($col1, 'beis_school_id') !== false) {
                                $header = $line;
                                $headerFound = true;
                                
                                // Dynamically find column indexes based on header names
                                foreach ($line as $idx => $colName) {
                                    $name = strtolower(trim($colName));
                                    if (stripos($name, 'district') !== false) $districtColIdx = $idx;
                                    if (stripos($name, 'sector') !== false) $sectorColIdx = $idx;
                                }
                            }
                            continue;
                        }

                        // Process Data Rows
                        if (count($line) >= 7) {
                            $sector = isset($line[$sectorColIdx]) ? strtolower(trim($line[$sectorColIdx])) : '';
                            $district = ($districtColIdx !== -1 && isset($line[$districtColIdx])) ? trim($line[$districtColIdx]) : '';
                            $classification = strtoupper(trim($line[$classColIdx])); 

                            // Collect district for the dropdown
                            if (!empty($district)) {
                                $availableDistricts[$district] = true;
                            }

                            // Filtering Logic
                            $matchesLevel = str_contains($classification, $levelKeyword) || str_contains($classification, 'ALL OFFERING');
                            $matchesSector = ($sector === strtolower($activeTab));
                            $matchesDistrict = empty($districtFilter) || (stripos($district, $districtFilter) !== false);

                            // SEARCH LOGIC INSIDE THE ROW
                            $matchesSearch = true;
                            if (!empty($search)) {
                                // Combine all columns in the row into one string to search across all cell data
                                $rowString = implode(' ', array_map('trim', $line));
                                if (stripos($rowString, $search) === false) {
                                    $matchesSearch = false;
                                }
                            }

                            if ($matchesSector && $matchesLevel && $matchesDistrict && $matchesSearch) {
                                $filteredRows[] = $line;
                            }
                        }
                    }
                    
                    $item->availableDistricts = array_keys($availableDistricts);
                    
                    // Paginate the filtered results
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
                    // Silently fail on parse error
                }
            }
        }
        
        return $item;
    }
}