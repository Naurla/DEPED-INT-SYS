<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter; // <--- This is usually the missing one
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
    }
}