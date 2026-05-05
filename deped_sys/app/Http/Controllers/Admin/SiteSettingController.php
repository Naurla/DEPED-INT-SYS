<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\MessageBag;

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
            'footer_sections.*.title' => 'required_with:footer_sections|string|max:255',
            'qr_link' => 'nullable|url',
        ]);

        $errors = new MessageBag();

        // --- 1. CHECK FOR DUPLICATE CONTACT DATA ---
        $emails = array_filter($request->contact_email ?? []);
        if (count($emails) !== count(array_unique(array_map('strtolower', $emails)))) {
            $errors->add('contact_email', 'Duplicate email addresses detected.');
        }

        $phones = array_filter($request->contact_phone ?? []);
        if (count($phones) !== count(array_unique($phones))) {
            $errors->add('contact_phone', 'Duplicate phone numbers detected.');
        }

        // --- 2. CHECK FOR DUPLICATE FOOTER CATEGORY TITLES ---
        $footerSections = $request->footer_sections ?? [];
        $titles = [];
        foreach ($footerSections as $section) {
            if (!empty($section['title'])) {
                $titles[] = strtolower(trim($section['title']));
            }
        }

        if (count($titles) !== count(array_unique($titles))) {
            $errors->add('footer_sections', 'Duplicate Footer Category titles detected. Each category must have a unique title.');
        }

        // Redirect back if custom duplicate errors found
        if ($errors->any()) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $settings = SiteSetting::first() ?? new SiteSetting();

        $settings->header_title = $request->header_title;
        $settings->footer_about = $request->footer_about;
        $settings->qr_link = $request->qr_link;
        
        // Clean and save arrays
        $settings->contact_email = array_values(array_unique(array_filter($request->contact_email ?? [])));
        $settings->contact_phone = array_values(array_unique(array_filter($request->contact_phone ?? [])));
        $settings->address = array_values(array_filter($request->address ?? []));
        
        // Format footer sections
        $sections = [];
        foreach ($footerSections as $section) {
            if (!empty($section['title'])) {
                $validLinks = [];
                if (isset($section['links'])) {
                    foreach ($section['links'] as $link) {
                        if (!empty($link['label']) && !empty($link['url'])) {
                            $validLinks[] = $link;
                        }
                    }
                }
                $sections[] = [
                    'title' => trim($section['title']),
                    'content' => $section['content'] ?? null,
                    'links' => $validLinks
                ];
            }
        }
        $settings->footer_sections = $sections;
        
        $settings->save();
        Cache::forget('site_settings');

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }

    public function toggleMaintenance(Request $request)
    {
        $settings = SiteSetting::first() ?? new SiteSetting();
        $settings->is_maintenance_mode = $request->boolean('is_maintenance_mode');
        $settings->disabled_pages = $request->input('disabled_pages', []); 
        $settings->save();

        return response()->json(['success' => true]);
    }
}