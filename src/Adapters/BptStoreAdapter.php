<?php

namespace FileStorage\Adapters;

use FileStorage\Drive\BptDrive;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use Throwable;

class BptStoreAdapter implements FilesystemAdapter
{
    public function __construct(protected BptDrive $drive) {}

    public function getUrl($path): ?string
    {
        return $this->drive->getPublicUrl($path);
    }

    public function fileExists(string $path): bool
    {
        try {
            return Str::isUuid($path) && $this->read($path);
        } catch (UnableToReadFile $e) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        return false;
    }

    public function write(string $path, string $contents, \League\Flysystem\Config $config): void
    {
        $resource = tmpfile();
        fwrite($resource, $contents);

        $this->writeTo($path, $resource, $config);
    }

    public function writeStream(string $path, $contents, \League\Flysystem\Config $config): void
    {
        $this->writeTo($path, $contents, $config);
    }

    private function writeTo(string $path, $contents, \League\Flysystem\Config $config): void
    {
        try {
            $file = $this->drive->add(
                $contents,
                $config->get('group', '1'),
                $config->get('visibility') === 'public'
            );
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage());
        }
    }

    public function read(string $path): string
    {
        try {
            return $this->drive->get($path);
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage());
        }
    }

    public function readStream(string $path)
    {
        $contents = $this->read($path);

        $resource = tmpfile();
        fwrite($resource, $contents);

        return $resource;
    }

    public function delete(string $path): void
    {
        $this->drive->delete($path);
    }

    public function deleteDirectory(string $path): void
    {
        throw new UnableToDeleteDirectory($path);
    }

    public function createDirectory(string $path, \League\Flysystem\Config $config): void
    {
        throw new UnableToCreateDirectory($path);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw new UnableToSetVisibility($path);
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
        $results = [];
        $page = 0;
        $perPage = 100;
        do {
            $page++;
            try {
                $files = $this->drive->all('', $page, $perPage);
            } catch (\Throwable $exception) {
                throw new \Exception('Failed to fetch '.$page.' page due to: '.
                    $exception->getMessage(), $exception->getCode(), $exception);
            }
            $results = array_merge($results, $files);

        } while ($perPage === count($files));

        return $results;
    }

    public function move(string $source, string $destination, \League\Flysystem\Config $config): void
    {
        throw UnableToMoveFile::because('not supported method', $source, $destination);
    }

    public function copy(string $source, string $destination, \League\Flysystem\Config $config): void
    {
        throw UnableToCopyFile::because('not supported method', $source, $destination);
    }
}
