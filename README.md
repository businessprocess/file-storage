File storage
=============================
![PHP 8.x](https://img.shields.io/badge/PHP-%5E8.0-blue)
[![Laravel 8.x](https://img.shields.io/badge/Laravel-8.x-orange.svg)](http://laravel.com)
[![Yii 2.x](https://img.shields.io/badge/Yii-2.x-orange)](https://www.yiiframework.com/doc/guide/2.0/ru)
![Latest Stable Version](https://poser.pugx.org/businessprocess/file-storage/v/stable)
![Release date](https://img.shields.io/github/release-date/businessprocess/file-storage)
![Release Version](https://img.shields.io/github/v/release/businessprocess/file-storage)
![Total Downloads](https://poser.pugx.org/businessprocess/file-storage/downloads)
![Pull requests](https://img.shields.io/bitbucket/pr/businessprocess/file-storage)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=plastic-square)](LICENSE)
![Stars](https://img.shields.io/github/stars/businessprocess/file-storage?style=social)

File storage to Laravel FrameWork v8.0 and above.

## Installation
The recommended way to install channel is through
[Composer](http://getcomposer.org).

```bash
composer require businessprocess/file-storage
```

## Usage BptDrive <a name="usege-drive"></a>

```php
    $config = [
        'url' => env('FILE_STORE_URL'),
        'login' => env('FILE_STORE_LOGIN'),
        'password' => env('FILE_STORE_PASS'),      
    ];

   $storage = new \FileStorage\Drive\BptDrive(new \FileStorage\Http\Client($config));

   $file = $storage->add('1', true, $content);

   $url = $storage->getPublicUrl($file->getHash());
```

## Usage FileStore Facade <a name="usege-facade"></a>

```php
   $file = \FileStorage\Facades\FileStore::add('1', true, $content);

   $url = \FileStorage\Facades\FileStore::getPublicUrl($file->getHash());
```

## Usage Storage <a name="usege-storage"></a>

```php
    $path = 'your-file-path'

   \Storage::drive('cloud')->put($path, $content, $config);

   $url = \Storage::drive('cloud')->url($path);
```

## Config <a name="usege-disk"></a>
```php
    'disks' => [
       'cloud' => [
        'driver' => 'bpt-store',      
        'group' => '1',
        'visibility' => 'public',
        'throw' => true,
        ],
    ]
```

## Config Voyager <a name="usege-voyager"></a>
```php
     'storage' => [
        'disk' => 'cloud',
    ],
```

#### Available Options

| Option         | Description               | Default value | 
|----------------|---------------------------|---------------|
| url            | API url (required)        | null          |
| login          | Login (required)          | null          |
| password       | Password (required)       | null          |

#### Available methods

| Option         | Description               | Default value | 
|----------------|---------------------------|---------------|
| url            | API url (required)        | null          |
| login          | Login (required)          | null          |
| password       | Password (required)       | null          |
