<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidOpportunity;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;

class ProcurementController extends Controller
{
    private $driveService;
    private $parentFolderId = '1OJCkFiAR3wdpiS-oCsoui_4HjHZQ-vDM'; // Keep your specific folder ID

    public function __construct()
    {
        $client = new Client();
        
        // 1. Grab variables from .env
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');

        // SAFETY CHECK 1: Did Laravel actually load the .env file?
        if (!$refreshToken) {
            dd("Error: Laravel cannot see your GOOGLE_REFRESH_TOKEN. Please run 'php artisan config:clear' in your terminal.");
        }

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->addScope(Drive::DRIVE);
        
        // 2. Fetch the access token
        $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        // SAFETY CHECK 2: Did Google reject the refresh token?
        if (isset($token['error'])) {
            dd("Google Authentication Failed! Here is the reason:", $token);
        }

        // 3. Explicitly tell the Google Client to use this token for all future requests
        $client->setAccessToken($token);

        // 4. Set the Drive service
        $this->driveService = new Drive($client);
    }

    /**
     * Display a listing of the resource.
     * THIS FIXES YOUR 500 ERROR!
     */
    public function index()
    {
        // Fetch the bid opportunities from the database, newest first
        $bidOpportunities = BidOpportunity::latest()->paginate(10);

        // Return the view and pass the data to it
        return view('admin.bid-opportunities.index', compact('bidOpportunities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'jpeg_file' => 'required|image|mimes:jpeg,jpg|max:5120',
            'pdf_file' => 'required|mimes:pdf|max:10240',
        ]);

        // 1. Get/Create Folder Structure: Bid Opportunities > Current Month & Year
        $categoryFolderId = $this->getOrCreateFolder('Bid Opportunities', $this->parentFolderId);
        $monthFolderId = $this->getOrCreateFolder(now()->format('F Y'), $categoryFolderId);

        // 2. Upload Files to Google Drive inside the specific month folder
        $jpegDriveId = $this->uploadToDrive($request->file('jpeg_file'), $monthFolderId);
        $pdfDriveId = $this->uploadToDrive($request->file('pdf_file'), $monthFolderId);

        // 3. Create Local Record (Storing Drive IDs instead of local paths)
        BidOpportunity::create([
            'title' => $request->title,
            'jpeg_path' => $jpegDriveId, // Store the Google Drive File ID
            'pdf_path' => $pdfDriveId,
        ]);

        return redirect()->route('admin.bid-opportunities.index')
                         ->with('success', 'Bid Opportunity uploaded to Google Drive successfully.');
    }

    /**
     * Helper to find or create a folder by name
     */
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
        
        // Restricted Access: Try to set permissions
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

    /**
     * Restrict access to DepEd Personnel (Domain-based)
     */
    private function setDomainPermissions($fileId)
    {
        try {
            $permission = new Permission([
                'type' => 'domain',
                'role' => 'reader',
                'domain' => 'deped.gov.ph' // Restricted to DepEd domain
            ]);

            $this->driveService->permissions->create($fileId, $permission);
        } catch (\Exception $e) {
            // Logs an error if a personal @gmail.com account cannot set Workspace domain permissions
            \Log::warning('Notice: Could not set domain permissions. This usually happens if the authenticating account is not part of the deped.gov.ph Workspace. Error: ' . $e->getMessage());
            
            // Optional fallback: Uncomment the lines below to make the file viewable by ANYONE with the link instead
            /*
            $publicPermission = new Permission(['type' => 'anyone', 'role' => 'reader']);
            $this->driveService->permissions->create($fileId, $publicPermission);
            */
        }
    }
}