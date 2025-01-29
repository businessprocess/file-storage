<?php

namespace FileStorage\Http;

use FileStorage\Contracts\Cache;
use FileStorage\Http\Response\Authenticate;
use GeoService\Cache\ArrayRepository;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * @method string get($url, $options = [], $tries = 2)
 * @method string post($url, $options = [], $tries = 2)
 * @method void delete($url, $options = [], $tries = 2)
 */
class Client
{
    private \GuzzleHttp\Client $client;

    public function __construct(protected $options, protected ?Cache $cache = null)
    {
        $this->processOptions();

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->options['url'],
            RequestOptions::CONNECT_TIMEOUT => $this->options['connect_timeout'] ?? 80,
            RequestOptions::TIMEOUT => $this->options['timeout'] ?? 30,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function cache(): Cache
    {
        if (is_null($this->cache)) {
            $this->cache = new ArrayRepository;
        }

        return $this->cache;
    }

    public function setCache(Cache $cache): Client
    {
        $this->cache = $cache;
    }

    public function processOptions(): void
    {
        if (! isset($this->options['url'])) {
            throw new \InvalidArgumentException('Url is required');
        }

        if (! isset($this->options['login']) || ! isset($this->options['password'])) {
            throw new \InvalidArgumentException('Login and password is required');
        }

        $this->options['url'] = trim($this->options['url'], '/').'/api/v1/';
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $uri, array $options = [], int $tries = 2): string
    {
        try {
            $response = $this->client->request($method, $this->resolveAliases($uri), array_merge($this->getHeaders(), $options));
        } catch (RequestException $e) {
            if ($e->getCode() === HttpResponse::HTTP_UNAUTHORIZED) {
                $this->cache()->forget(__CLASS__);
            }
            if ($tries > 0) {
                $tries--;

                return $this->request($method, $uri, $options, $tries);
            }
            throw $e;
        }

        return $response->getBody()->getContents();
    }

    public function resolveAliases(string $string): string
    {
        $replacements = [
            '/{baseUrl}/' => $this->options['url'],
            '/{userUuid}/' => $this->auth()->getUserUuid(),
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $string);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(): array
    {
        $response = $this->client->post('login', [
            RequestOptions::JSON => [
                'login' => $this->options['login'],
                'password' => $this->options['password'],
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function auth(): Authenticate
    {
        return $this->cache->remember(
            __CLASS__,
            43200,
            fn () => new Authenticate($this->login())
        );
    }

    private function getHeaders(): array
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => $this->auth()->getAuthToken(),
            ],
        ];
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->request($name, ...$arguments);
    }
}
