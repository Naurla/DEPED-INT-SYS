<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        // Fetch ALL pages and load their parent so we can show the hierarchy
        $pages = Page::with('parent')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $allPages = Page::all(); // To populate the Parent Dropdown
        return view('admin.pages.create', compact('allPages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', 
            'layout_template' => 'required|string'
        ]);

        Page::create([
            'parent_id' => $request->parent_id ?: null,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        $allPages = Page::where('id', '!=', $page->id)->get();
        return view('admin.pages.edit', compact('page', 'allPages'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', 
            'layout_template' => 'required|string'
        ]);

        $page->update([
            'parent_id' => $request->parent_id ?: null,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        Cache::forget('nav_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    // --- CKEDITOR IMAGE UPLOAD METHOD ---
    public function uploadImage(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                
                // Using uniqid() ensures no weird filename characters break the upload
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
                // FIX: Explicitly set the path to 'pages/images' and the disk to 'public'
                $file->storeAs('pages/images', $fileName, 'public');
        
                // FIX: Use asset() helper to generate the correct absolute URL
                $url = asset('storage/pages/images/' . $fileName);
        
                // Return exactly what CKEditor expects
                return response()->json([
                    'uploaded' => 1,
                    'fileName' => $fileName,
                    'url' => $url
                ]);
            }
            
            return response()->json(['uploaded' => 0, 'error' => ['message' => 'No file found in request.']]);
        } catch (\Exception $e) {
            // If anything goes wrong (like folder permissions), return the exact error
            return response()->json(['uploaded' => 0, 'error' => ['message' => $e->getMessage()]]);
        }
    }

    // --- VIDEO SHAPE DETECTOR API ---
    public function checkVideoShape(Request $request)
    {
        $url = $request->query('url');
        $isVertical = false; // Default assumption is Horizontal
        
        try {
            // 1. YouTube API Check
            if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                if (str_contains($url, '/shorts/')) {
                    $isVertical = true;
                } else {
                    $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
                    $response = json_decode(@file_get_contents($oembedUrl), true);
                    if (isset($response['width']) && isset($response['height'])) {
                        $isVertical = $response['height'] > $response['width'];
                    }
                }
            } 
            // 2. TikTok Default Check
            elseif (str_contains($url, 'tiktok.com')) {
                $isVertical = true;
            } 
            // 3. Facebook Scraper Check
            elseif (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch') || str_contains($url, 'fb.me')) {
                // Emulate a browser so Facebook lets us read the hidden Meta Tags
                $options = [
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
                    ]
                ];
                $context = stream_context_create($options);
                $html = @file_get_contents($url, false, $context);
                
                if ($html) {
                    // Try to extract the real video dimensions from OpenGraph Meta Tags
                    preg_match('/<meta property="og:video:width" content="(\d+)"/i', $html, $widthMatch);
                    preg_match('/<meta property="og:video:height" content="(\d+)"/i', $html, $heightMatch);
                    
                    if (empty($widthMatch)) preg_match('/<meta property="og:image:width" content="(\d+)"/i', $html, $widthMatch);
                    if (empty($heightMatch)) preg_match('/<meta property="og:image:height" content="(\d+)"/i', $html, $heightMatch);

                    if (!empty($widthMatch) && !empty($heightMatch)) {
                        $width = (int) $widthMatch[1];
                        $height = (int) $heightMatch[1];
                        $isVertical = $height > $width;
                    } else {
                        // Fallback if Facebook completely blocks the scraper
                        $isVertical = str_contains($url, '/reel/');
                    }
                } else {
                    $isVertical = str_contains($url, '/reel/');
                }
            }
        } catch (\Exception $e) {
            // If anything fails, it falls back cleanly to horizontal to avoid breaking the page
        }

        return response()->json([
            'shape' => $isVertical ? 'vertical' : 'horizontal',
            'url' => $url
        ]);
    }
}