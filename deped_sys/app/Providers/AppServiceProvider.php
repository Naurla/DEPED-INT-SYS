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
        // --- 1. EXISTING GOOGLE DRIVE EXTENSION ---
        Storage::extend('google', function ($app, $config) {
            $client = new GoogleClient();
            $client->setAuthConfig($config['credentials']);
            $client->addScope(GoogleServiceDrive::DRIVE);

            $service = new GoogleServiceDrive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder']);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });

        // --- 2. GLOBAL RECENT UPDATES FOR APP LAYOUT ---
        // This ensures $globalRecentAdvisories and $globalRecentMemoranda 
        // are available in your layouts.app file on every page.
        View::composer('layouts.app', function ($view) {
            $view->with('globalRecentAdvisories', Issuance::where('type', 'advisory')
                ->latest()
                ->take(5)
                ->get());

            $view->with('globalRecentMemoranda', Issuance::where('type', 'memorandum')
                ->latest()
                ->take(5)
                ->get());
        });
    }
}