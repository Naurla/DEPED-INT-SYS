<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;
use Illuminate\Support\Facades\View; // Added for global view sharing
use App\Models\Issuance;             // Added to fetch recent updates
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
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

        $view->with('site_settings', $site_settings)
             ->with('site_logos', $site_logos);
    });
}
}