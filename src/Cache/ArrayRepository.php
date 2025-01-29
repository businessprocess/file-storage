<?php

namespace GeoService\Cache;

use Closure;
use FileStorage\Contracts\Cache;

class ArrayRepository implements Cache
{
    private array $data = [];

    public function remember($key, $ttl, Closure $callback): mixed
    {
        if (! isset($this->data[$key])) {
            $this->data[$key] = $callback();
        }

        return $this->data[$key];
    }

    public function forget($key): void
    {
        unset($this->data[$key]);
    }
}
