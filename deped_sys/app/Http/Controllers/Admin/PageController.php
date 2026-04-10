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
        $pages = Page::with('parent')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $allPages = Page::all(); 
        return view('admin.pages.create', compact('allPages'));
    }

    private function formatVideosArray($rawVideos)
    {
        $videos = [];
        if (is_array($rawVideos)) {
            foreach ($rawVideos as $v) {
                if (!empty($v['url'])) {
                    $videos[] = [
                        'url' => $v['url'],
                        'shape' => $v['shape'] ?? 'landscape'
                    ];
                }
            }
        }
        return empty($videos) ? null : $videos;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable', 
            'layout_template' => 'required|string',
            'parent_selection' => 'nullable|string', // <-- Unified Field
            'featured_videos' => 'nullable|array',
            'featured_videos.*.url' => 'nullable|url',
            'featured_videos.*.shape' => 'nullable|in:landscape,portrait'
        ]);

        $parentId = null;
        $menuLocation = null;

        // Check the unified dropdown
        if ($request->filled('parent_selection')) {
            if (str_starts_with($request->parent_selection, 'menu_')) {
                // If it's a hardcoded menu (e.g., 'menu_issuances')
                $menuLocation = str_replace('menu_', '', $request->parent_selection);
            } else {
                // If it's a dynamic page ID (e.g., '5')
                $parentId = $request->parent_selection;
            }
        }

        Page::create([
            'parent_id' => $parentId,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'menu_location' => $menuLocation,
            'featured_videos' => $this->formatVideosArray($request->featured_videos),
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        Cache::forget('categorized_pages');
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
            'layout_template' => 'required|string',
            'parent_selection' => 'nullable|string', // <-- Unified Field
            'featured_videos' => 'nullable|array',
            'featured_videos.*.url' => 'nullable|url',
            'featured_videos.*.shape' => 'nullable|in:landscape,portrait'
        ]);

        $parentId = null;
        $menuLocation = null;

        if ($request->filled('parent_selection')) {
            if (str_starts_with($request->parent_selection, 'menu_')) {
                $menuLocation = str_replace('menu_', '', $request->parent_selection);
            } else {
                $parentId = $request->parent_selection;
            }
        }

        $page->update([
            'parent_id' => $parentId,
            'title' => $request->title,
            'content' => $request->content,
            'layout_template' => $request->layout_template,
            'menu_location' => $menuLocation,
            'featured_videos' => $this->formatVideosArray($request->featured_videos),
            'show_in_nav' => $request->has('show_in_nav')
        ]);

        Cache::forget('nav_pages');
        Cache::forget('categorized_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        Cache::forget('nav_pages');
        Cache::forget('categorized_pages');
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    // --- CKEDITOR IMAGE UPLOAD METHOD ---
    public function uploadImage(Request $request)
    {
        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('pages/images', $fileName, 'public');
                $url = asset('storage/pages/images/' . $fileName);
                
                return response()->json([
                    'uploaded' => 1,
                    'fileName' => $fileName,
                    'url' => $url
                ]);
            }
            
            return response()->json(['uploaded' => 0, 'error' => ['message' => 'No file found in request.']]);
        } catch (\Exception $e) {
            return response()->json(['uploaded' => 0, 'error' => ['message' => $e->getMessage()]]);
        }
    }

    // --- VIDEO SHAPE DETECTOR API ---
    public function checkVideoShape(Request $request)
    {
        $url = $request->query('url');
        $isVertical = false; 
        
        try {
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
            elseif (str_contains($url, 'tiktok.com')) {
                $isVertical = true;
            } 
            elseif (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch') || str_contains($url, 'fb.me')) {
                $options = [
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
                    ]
                ];
                $context = stream_context_create($options);
                $html = @file_get_contents($url, false, $context);
                
                if ($html) {
                    preg_match('/<meta property="og:video:width" content="(\d+)"/i', $html, $widthMatch);
                    preg_match('/<meta property="og:video:height" content="(\d+)"/i', $html, $heightMatch);
                    
                    if (empty($widthMatch)) preg_match('/<meta property="og:image:width" content="(\d+)"/i', $html, $widthMatch);
                    if (empty($heightMatch)) preg_match('/<meta property="og:image:height" content="(\d+)"/i', $html, $heightMatch);

                    if (!empty($widthMatch) && !empty($heightMatch)) {
                        $width = (int) $widthMatch[1];
                        $height = (int) $heightMatch[1];
                        $isVertical = $height > $width;
                    } else {
                        $isVertical = str_contains($url, '/reel/');
                    }
                } else {
                    $isVertical = str_contains($url, '/reel/');
                }
            }
        } catch (\Exception $e) {}

        return response()->json([
            'shape' => $isVertical ? 'vertical' : 'horizontal',
            'url' => $url
        ]);
    }
}