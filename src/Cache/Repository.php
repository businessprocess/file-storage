<?php

namespace FileStorage\Cache;

use Closure;
use FileStorage\Contracts\Cache;
use Illuminate\Cache\Repository as IlluminateRepository;

class Repository implements Cache
{
    public function __construct(protected IlluminateRepository $cache) {}

    public function remember($key, $ttl, Closure $callback): mixed
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    public function forget($key): void
    {
        $this->cache->forget($key);
    }
}
