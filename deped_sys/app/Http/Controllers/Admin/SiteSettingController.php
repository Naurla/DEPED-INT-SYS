<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first() ?? new SiteSetting();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'header_title' => 'required|string|max:255',
            'footer_about' => 'nullable|string',
            'contact_email' => 'nullable|array',
            'contact_email.*' => 'nullable|email',
            'contact_phone' => 'nullable|array',
            'contact_phone.*' => 'nullable|string',
            'address' => 'nullable|array',
            'address.*' => 'nullable|string',
            'footer_sections' => 'nullable|array',
            'qr_link' => 'nullable|url', // <-- Validate the qr_link as a URL
        ]);

        $settings = SiteSetting::first() ?? new SiteSetting();

        $settings->header_title = $request->header_title;
        $settings->footer_about = $request->footer_about;
        $settings->qr_link = $request->qr_link; // <-- Save the qr_link to the database
        
        // Re-index arrays to prevent gaps
        $settings->contact_email = array_values(array_filter($request->contact_email ?? []));
        $settings->contact_phone = array_values(array_filter($request->contact_phone ?? []));
        $settings->address = array_values(array_filter($request->address ?? []));
        
        // Format and clean up the dynamic footer categories
        $sections = [];
        if ($request->footer_sections) {
            foreach ($request->footer_sections as $section) {
                if (!empty($section['title'])) {
                    $validLinks = [];
                    if (isset($section['links'])) {
                        foreach ($section['links'] as $link) {
                            if (!empty($link['label'])) {
                                $validLinks[] = $link;
                            }
                        }
                    }
                    $sections[] = [
                        'title' => $section['title'],
                        'content' => $section['content'] ?? null, // <-- We now save the text content
                        'links' => $validLinks
                    ];
                }
            }
        }
        $settings->footer_sections = $sections;
        
        $settings->save();

        Cache::forget('site_settings');

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}