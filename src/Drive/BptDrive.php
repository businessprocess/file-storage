<?php

namespace FileStorage\Drive;

use FileStorage\Http\Client;
use FileStorage\Models\File;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class BptDrive
{
    public function __construct(protected Client $client) {}

    public function getPublicUrl(string $hash): string
    {
        return $this->client->resolveAliases("{baseUrl}/files/{$hash}");
    }

    public function add($content, $groupId, $isPublic = true): File
    {
        $response = $this->client->post('users/{userUuid}/files', [
            RequestOptions::MULTIPART => [
                [
                    'name' => 'groupId',
                    'contents' => $groupId,
                ],
                [
                    'name' => 'isPublic',
                    'contents' => $isPublic,
                ],
                [
                    'name' => 'file',
                    'contents' => $content instanceof \SplFileInfo ? fopen($content, 'rb') : $content,
                ],
            ],
        ]);

        if (empty($response)) {
            throw new UploadException('File could not be uploaded');
        }

        return new File(json_decode($response, true));
    }

    public function delete($uuid): void
    {
        $this->client->delete('users/{userUuid}/files/'.$uuid);
    }

    public function get($uuid): string
    {
        return $this->client->get('users/{userUuid}/files/'.$uuid);
    }

    public function all($query = '', $page = 1, $pageSize = 10, $isPublic = true): array
    {
        $response = $this->client->get('files', [
            RequestOptions::QUERY => [
                'userUuid' => $this->client->auth()->getUserUuid(),
                'pageNum' => $page,
                'pageSize' => $pageSize,
                'query' => $query,
                'isPublic' => $isPublic,
            ],
        ]);

        $response = json_decode($response, true);

        return array_map(fn ($item) => new File($item), $response['items'] ?? []);
    }
}
