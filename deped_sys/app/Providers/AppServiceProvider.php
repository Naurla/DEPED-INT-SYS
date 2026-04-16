<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;
use Illuminate\Support\Facades\View; 
use App\Models\Issuance;             
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema; 
use App\Models\Page; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $site_settings = \Illuminate\Support\Facades\Cache::rememberForever('site_settings', function () {
                return \App\Models\SiteSetting::first();
            });

            // Fetch all active logos sorted by their order
            $site_logos = \Illuminate\Support\Facades\Cache::rememberForever('site_logos', function () {
                return \App\Models\SiteLogo::where('is_active', true)->orderBy('order', 'asc')->get();
            });

            // --- FETCH DYNAMIC PAGES ---
            $navPages = collect(); 
            $categorizedPages = collect(); 
            
            if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                // FIXED: Fetch only items for the main bar that have NO parent AND NO menu location
                $navPages = \Illuminate\Support\Facades\Cache::rememberForever('nav_pages', function () {
                    return \App\Models\Page::whereNull('parent_id')
                                ->where(function($q) {
                                    $q->whereNull('menu_location')
                                      ->orWhere('menu_location', 'none')
                                      ->orWhere('menu_location', '');
                                })
                                ->where('show_in_nav', true)
                                ->with('children')
                                ->get();
                });

                // Fetch Custom pages specifically assigned to existing hardcoded dropdowns
                $categorizedPages = \Illuminate\Support\Facades\Cache::rememberForever('categorized_pages', function () {
                    return \App\Models\Page::whereNotNull('menu_location')
                                ->whereNotIn('menu_location', ['none', ''])
                                ->where('show_in_nav', true)
                                ->with(['children' => function($query) {
                                    $query->where('show_in_nav', true);
                                }])
                                ->get()
                                ->groupBy('menu_location');
                });
            }

            // --- FETCH RECENT ISSUANCES FOR FOOTER ---
            $globalRecentAdvisories = collect();
            $globalRecentMemoranda = collect();

            if (\Illuminate\Support\Facades\Schema::hasTable('issuances')) {
                $globalRecentAdvisories = Issuance::where('type', 'advisory')
                                            ->latest('created_at')
                                            ->take(3)
                                            ->get();

                $globalRecentMemoranda = Issuance::where('type', 'memorandum')
                                            ->latest('created_at')
                                            ->take(3)
                                            ->get();
            }

            // Share variables with all views
            $view->with('site_settings', $site_settings)
                 ->with('site_logos', $site_logos)
                 ->with('navPages', $navPages)
                 ->with('categorizedPages', $categorizedPages)
                 ->with('globalRecentAdvisories', $globalRecentAdvisories)
                 ->with('globalRecentMemoranda', $globalRecentMemoranda); 
        });
    }
}