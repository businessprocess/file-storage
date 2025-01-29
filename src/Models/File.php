<?php

namespace FileStorage\Models;

use League\Flysystem\FileAttributes;

class File extends FileAttributes
{
    protected ?int $id;

    protected ?string $hash;

    protected ?string $name;

    protected ?string $groupId;

    protected ?string $status;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->hash = $data['hash'] ?? null;
        $this->groupId = $data['groupId'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->status = $data['status'] ?? null;

        $path = $data['uuid'] ?? '';
        $mimeType = $data['mimeType'] ?? null;
        $fileSize = $data['size'] ?? null;
        $extraMetadata = $data['metadata'] ?? [];

        if ($lastModified = $data['createdAt'] ?? null) {
            $lastModified = strtotime($lastModified);
        }
        if (! is_null($visibility = $data['isPublic'] ?? null)) {
            $visibility = $visibility ? 'public' : 'private';
        }

        parent::__construct(
            $path,
            $fileSize,
            $visibility,
            $lastModified,
            $mimeType,
            $extraMetadata,
        );
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getHash(): mixed
    {
        return $this->hash;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function getGroupId(): mixed
    {
        return $this->groupId;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }
}
