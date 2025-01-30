<?php

namespace FileStorage\Adapters;

use FileStorage\Drive\BptDrive;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Traits\ForwardsCalls;

class FilesystemAdapter
{
    use ForwardsCalls;

    public function __construct(protected BptDrive $drive, protected Filesystem $filesystem, protected $config = []) {}

    public function url($path)
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return $this->filesystem->url($path);
    }

    public function put(&$path, $contents, $options = [])
    {
        $resource = tmpfile();
        fwrite($resource, $contents);

        $file = $this->drive->add(
            $contents,
            $this->config['group'] ?? '1',
            $this->config['visibility'] === 'public'
        );

        $path = $this->drive->getPublicUrl($file->getHash());
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->forwardCallTo($this->filesystem, $name, $arguments);
    }
}
