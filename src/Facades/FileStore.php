<?php

namespace FileStorage\Facades;

use FileStorage\Drive\BptDrive;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin BptDrive
 */
class FileStore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'file-storage';
    }
}
