<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
// Removed Illuminate\Support\Facades\Storage since we are using Google Drive now
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;

class IssuanceController extends Controller
{
    private $driveService;
    // IMPORTANT: Paste the Google Drive folder ID where you want Issuances saved
    private $parentFolderId = 'YOUR_PARENT_FOLDER_ID_HERE'; 

    public function __construct()
    {
        // Initialize the Google Client with your .env credentials
        $client = new Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->addScope(Drive::DRIVE);
        
        // Automatically exchange the refresh token for a fresh access token
        $client->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

        // Set the Drive service
        $this->driveService = new Drive($client);
    }

    // --- PUBLIC VIEWS (Kept exactly as you wrote them) ---
    public function advisories()
    {
        $items = Issuance::where('type', 'advisory')->latest()->paginate(10);
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Advisories',
            'color' => 'red',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function memoranda()
    {
        $items = Issuance::where('type', 'memorandum')->latest()->paginate(10);
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'Division Memoranda',
            'color' => 'blue',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function hrmpsb()
    {
        $items = Issuance::where('type', 'hrmpsb')->latest()->paginate(10);
        
        $recentAdvisories = Issuance::where('type', 'advisory')->latest()->take(5)->get();
        $recentMemoranda = Issuance::where('type', 'memorandum')->latest()->take(5)->get();
        
        return view('issuances.category', [
            'items' => $items,
            'title' => 'HRMPSB Assessment Results',
            'color' => 'yellow',
            'recentAdvisories' => $recentAdvisories,
            'recentMemoranda' => $recentMemoranda,
        ]);
    }

    public function show(Issuance $issuance)
    {
        // Fetch recent advisories (excluding the current one)
        $recentAdvisories = Issuance::where('type', 'advisory')
                                    ->where('id', '!=', $issuance->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // Fetch recent memoranda (excluding the current one)
        $recentMemoranda = Issuance::where('type', 'memorandum')
                                   ->where('id', '!=', $issuance->id)
                                   ->latest()
                                   ->take(5)
                                   ->get();

        return view('issuances.show', compact('issuance', 'recentAdvisories', 'recentMemoranda'));
    }

    // --- ADMIN CRUD METHODS (Updated for Google Drive) ---
    public function adminIndex(Request $request)
    {
        // Get the type from the URL (e.g., ?type=memorandum), default to advisory
        $type = $request->query('type', 'advisory'); 
        
        $issuances = Issuance::where('type', $type)->latest()->paginate(10);
        
        return view('admin.issuances.index', compact('issuances', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'type' => 'required|in:advisory,memorandum,hrmpsb',
            'pdf_file' => 'required|mimes:pdf|max:10240', // Max 10MB PDF
        ]);

        // 1. Create folder structure: Issuances > Type (e.g., Advisory)
        $categoryFolderId = $this->getOrCreateFolder('Issuances', $this->parentFolderId);
        $typeFolderId = $this->getOrCreateFolder(ucfirst($validated['type']), $categoryFolderId);

        // 2. Upload the file to the specific type folder
        $pdfDriveId = $this->uploadToDrive($request->file('pdf_file'), $typeFolderId);
        
        // 3. Save to database using the Google Drive ID
        Issuance::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'pdf_path' => $pdfDriveId,
        ]);

        return back()->with('success', ucfirst($validated['type']) . ' uploaded successfully!');
    }

    public function update(Request $request, Issuance $issuance)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        // Replace PDF if a new one is uploaded
        if ($request->hasFile('pdf_file')) {
            
            // Delete old file from Google Drive
            if ($issuance->pdf_path) {
                try {
                    $this->driveService->files->delete($issuance->pdf_path);
                } catch (\Exception $e) {
                    \Log::warning('Could not delete old Drive file: ' . $e->getMessage());
                }
            }
            
            // Upload the new file to Google Drive
            $categoryFolderId = $this->getOrCreateFolder('Issuances', $this->parentFolderId);
            $typeFolderId = $this->getOrCreateFolder(ucfirst($issuance->type), $categoryFolderId);
            
            $dataToUpdate['pdf_path'] = $this->uploadToDrive($request->file('pdf_file'), $typeFolderId);
        }

        $issuance->update($dataToUpdate);

        return back()->with('success', 'Issuance updated successfully!');
    }

    public function destroy(Issuance $issuance)
    {
        // Delete PDF file from Google Drive
        if ($issuance->pdf_path) {
            try {
                $this->driveService->files->delete($issuance->pdf_path);
            } catch (\Exception $e) {
                \Log::warning('Could not delete Drive file during record deletion: ' . $e->getMessage());
            }
        }
        
        // Delete database record
        $issuance->delete();

        return back()->with('success', 'Issuance deleted successfully!');
    }

    // --- GOOGLE DRIVE HELPER METHODS ---

    private function getOrCreateFolder($name, $parentId)
    {
        $query = "name = '$name' and mimeType = 'application/vnd.google-apps.folder' and '$parentId' in parents and trashed = false";
        $results = $this->driveService->files->listFiles(['q' => $query]);

        if (count($results->getFiles()) > 0) {
            return $results->getFiles()[0]->getId();
        }

        $folderMetadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId]
        ]);

        $folder = $this->driveService->files->create($folderMetadata, ['fields' => 'id']);
        
        // Try to set DepEd domain permissions (Falls back quietly if unable)
        $this->setDomainPermissions($folder->getId());

        return $folder->getId();
    }

    private function uploadToDrive($file, $parentId)
    {
        $fileMetadata = new DriveFile([
            'name' => time() . '_' . $file->getClientOriginalName(),
            'parents' => [$parentId]
        ]);

        $content = file_get_contents($file->getRealPath());
        $uploadedFile = $this->driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        return $uploadedFile->id;
    }

    private function setDomainPermissions($fileId)
    {
        try {
            $permission = new Permission([
                'type' => 'domain',
                'role' => 'reader',
                'domain' => 'deped.gov.ph' 
            ]);

            $this->driveService->permissions->create($fileId, $permission);
        } catch (\Exception $e) {
            \Log::warning('Notice: Could not set domain permissions. Error: ' . $e->getMessage());
        }
    }
}