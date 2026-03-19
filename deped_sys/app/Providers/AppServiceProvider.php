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

            // --- UPDATED: Fetch hierarchical dynamic pages for navigation ---
            $navPages = collect(); 
            
            if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                $navPages = \Illuminate\Support\Facades\Cache::rememberForever('nav_pages', function () {
                    // Only get root pages (no parent) but eager load all nested children
                    // REMOVED the show_in_nav filter so Admin can see hidden pages
                    return \App\Models\Page::whereNull('parent_id')
                                ->with('children')
                                ->get();
                });
            }

            // Share variables with all views
            $view->with('site_settings', $site_settings)
                 ->with('site_logos', $site_logos)
                 ->with('navPages', $navPages); 
        });
    }
}