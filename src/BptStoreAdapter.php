<?php

namespace FileStorage;

use FileStorage\Drive\Driver;
use FileStorage\Models\File;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use Throwable;

class BptStoreAdapter implements FilesystemAdapter
{
    private array $files = [];

    public function __construct(protected Driver $driver) {}

    private function getFile($path): ?File
    {
        return data_get($this->files, $path);
    }

    private function setFile($path, $file): void
    {
        data_set($this->files, $path, $file);
    }

    public function getUrl($path): string
    {
        return $this->driver->getPublicUrl($path);
    }

    public function fileExists(string $path): bool
    {
        if ($file = $this->getFile($path)) {
            return (bool) $this->driver->get($file->getUuid());
        }

        return false;
    }

    public function directoryExists(string $path): bool
    {
        return false;
    }

    public function write(string $path, string $contents, \League\Flysystem\Config $config): void
    {
        try {
            $file = $this->driver->add(
                $config->get('group', '1'),
                $config->get('isPublic', true),
                $contents
            );

            $this->setFile($path, $file);
        } catch (Throwable $e) {
            throw new UnableToWriteFile;
        }
    }

    public function writeStream(string $path, $contents, \League\Flysystem\Config $config): void
    {
        $this->write($path, $contents, $config);
    }

    public function read(string $path): string
    {
        if ($file = $this->getFile($path)) {
            return $this->driver->get($file->getUuid());
        }

        throw new UnableToReadFile;
    }

    public function readStream(string $path)
    {
        return $this->read($path);
    }

    public function delete(string $path): void
    {
        if ($file = $this->getFile($path)) {
            $this->driver->delete($file->getUuid());
        }

        throw new UnableToDeleteFile;
    }

    public function deleteDirectory(string $path): void
    {
        throw new UnableToDeleteDirectory;
    }

    public function createDirectory(string $path, \League\Flysystem\Config $config): void
    {
        throw new UnableToCreateDirectory;
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw new UnableToSetVisibility;
    }

    public function visibility(string $path): \League\Flysystem\FileAttributes
    {
        return new \League\Flysystem\FileAttributes($path);
    }

    public function mimeType(string $path): \League\Flysystem\FileAttributes
    {
        return new \League\Flysystem\FileAttributes($path);
    }

    public function lastModified(string $path): \League\Flysystem\FileAttributes
    {
        return new \League\Flysystem\FileAttributes($path);
    }

    public function fileSize(string $path): \League\Flysystem\FileAttributes
    {
        return new \League\Flysystem\FileAttributes($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return collect($this->driver->all());
    }

    public function move(string $source, string $destination, \League\Flysystem\Config $config): void
    {
        throw new UnableToMoveFile;
    }

    public function copy(string $source, string $destination, \League\Flysystem\Config $config): void
    {
        throw new UnableToCopyFile;
    }
}
