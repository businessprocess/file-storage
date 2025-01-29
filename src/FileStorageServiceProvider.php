<?php

namespace FileStorage;

use FileStorage\Cache\Repository;
use FileStorage\Drive\Driver;
use FileStorage\Http\Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;

class FileStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/file-storage.php', 'file-storage');

        $this->publishes([
            __DIR__.'/../config/file-storage.php' => config_path('file-storage.php'),
        ]);

        $this->app->bind('file-storage', function ($app) {
            $config = $app['config']['file-storage'];

            $client = new Client($config, new Repository($app['cache']));

            return new Driver($client);
        });

        Storage::extend('bpt-store', function ($app, $config) {
            $adapter = new BptStoreAdapter($app->make('file-storage'));

            return new FilesystemAdapter($this->createFlysystem($adapter, $config), $adapter, $config);
        });
    }

    public function register()
    {
        //
    }

    protected function createFlysystem(FlysystemAdapter $adapter, array $config)
    {
        return new Flysystem($adapter, Arr::only($config, [
            'directory_visibility',
            'disable_asserts',
            'temporary_url',
            'url',
            'visibility',
        ]));
    }
}
