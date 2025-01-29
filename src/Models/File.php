<?php

namespace FileStorage\Models;

class File
{
    protected $id;

    protected $hash;

    protected $uuid;

    protected $name;

    protected $createdAt;

    protected $mimeType;

    protected $size;

    protected $groupId;

    protected $isPublic;

    protected $status;

    protected $metadata;

    public function __construct($data = null)
    {
        $this->id = $data['id'] ?? null;
        $this->hash = $data['hash'] ?? null;
        $this->uuid = $data['uuid'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->mimeType = $data['mimeType'] ?? null;
        $this->size = $data['size'] ?? null;
        $this->groupId = $data['groupId'] ?? null;
        $this->isPublic = $data['isPublic'] ?? false;
        $this->status = $data['status'] ?? null;
        $this->metadata = $data['metadata'] ?? [];
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getHash(): mixed
    {
        return $this->hash;
    }

    public function getUuid(): mixed
    {
        return $this->uuid;
    }

    public function getName(): mixed
    {
        return $this->name;
    }

    public function getCreatedAt(): mixed
    {
        return $this->createdAt;
    }

    public function getMimeType(): mixed
    {
        return $this->mimeType;
    }

    public function getSize(): mixed
    {
        return $this->size;
    }

    public function getGroupId(): mixed
    {
        return $this->groupId;
    }

    public function getIsPublic(): mixed
    {
        return $this->isPublic;
    }

    public function getStatus(): mixed
    {
        return $this->status;
    }

    public function getMetadata(): mixed
    {
        return $this->metadata;
    }
}
