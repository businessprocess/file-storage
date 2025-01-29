<?php

namespace FileStorage\Facades;

use FileStorage\Drive\Driver;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin Driver
 */
class FileStore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'file-storage';
    }
}
