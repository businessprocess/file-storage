<?php

namespace FileStorage;

use FileStorage\Cache\Repository;
use FileStorage\Drive\BptDrive;
use FileStorage\Http\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class FileStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/file-storage.php', 'file-storage');

        $this->publishes([
            __DIR__.'/../config/file-storage.php' => config_path('file-storage.php'),
        ]);

        $this->app->singleton('file-storage', function ($app) {
            $config = $app['config']['file-storage'];

            $client = new Client($config, new Repository($app['cache.store']));

            return new BptDrive($client);
        });

        Storage::extend('bpt-store', function ($app, $config) {
            return new Adapters\FilesystemAdapter($app->make('file-storage'), $app->make('filesystem.disk'), $config);
        });
    }

    public function register()
    {
        //
    }
}
