<?php

namespace FileStorage\Drive;

use FileStorage\Http\Client;
use FileStorage\Models\File;
use GuzzleHttp\RequestOptions;

class Driver
{
    public function __construct(protected Client $client) {}

    public function getPublicUrl(string $hash): string
    {
        return $this->client->resolveAliases("{baseUrl}/files/{$hash}");
    }

    public function add($groupId, $isPublic, $content): File
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
                    'contents' => $content,
                ],
            ],
        ]);

        return new File(json_decode($response, true));
    }

    public function delete($uuid): void
    {
        $this->client->delete('users/{userUuid}/files/'.$uuid);
    }

    public function get($uuid): ?string
    {
        try {
            return $this->client->get('users/{userUuid}/files/'.$uuid);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function all($query = '', $page = 1, $pageSize = 10): array
    {
        $response = $this->client->get('files', [
            RequestOptions::QUERY => [
                'userUuid' => $this->client->auth()->getUserUuid(),
                'page' => $page,
                'pageSize' => $pageSize,
                'query' => $query,
            ],
        ]);

        $response = json_decode($response, true);

        return array_map(fn ($item) => new File($item), $response['items'] ?? []);
    }
}
