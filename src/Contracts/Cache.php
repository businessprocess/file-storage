<?php

namespace FileStorage\Contracts;

use Closure;

interface Cache
{
    public function remember($key, $ttl, Closure $callback): mixed;

    public function forget($key): void;
}
