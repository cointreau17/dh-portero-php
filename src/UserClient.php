<?php

declare(strict_types=1);

namespace Dh\Portero;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserClient
{
    private const CACHE_PREFIX      = 'dh_user_name_';
    private const CACHE_PREFIX_UUID = 'dh_user_uuid_';
    private const CACHE_TTL         = 3600;
    private const CACHE_TTL_ERR     = 60;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly string $baseUrl,
    ) {}

    public function getUserUuidByAuth0Id(string $auth0Id): ?string
    {
        $cacheKey = self::CACHE_PREFIX_UUID . preg_replace('/[^a-zA-Z0-9_]/', '_', $auth0Id);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($auth0Id): ?string {
            try {
                $response = $this->httpClient->request(
                    'GET',
                    rtrim($this->baseUrl, '/') . '/user/auth0/' . rawurlencode($auth0Id) . '/uuid',
                    [
                        'verify_peer' => false,
                        'verify_host' => false,
                        'timeout'     => 3,
                    ]
                );

                $data = $response->toArray();
                $uuid = $data['uuid'] ?? null;

                $item->expiresAfter(self::CACHE_TTL);

                return $uuid;
            } catch (\Throwable) {
                $item->expiresAfter(self::CACHE_TTL_ERR);

                return null;
            }
        });
    }

    public function getUserName(string $uuid): ?string
    {
        // La clave de caché no puede contener guiones en Symfony Cache
        $cacheKey = self::CACHE_PREFIX . str_replace('-', '_', $uuid);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($uuid): ?string {
            try {
                $response = $this->httpClient->request(
                    'GET',
                    rtrim($this->baseUrl, '/') . '/user/' . $uuid . '/name',
                    [
                        'verify_peer' => false,
                        'verify_host' => false,
                        'timeout'     => 3,
                    ]
                );

                $data = $response->toArray();
                $name = $data['name'] ?? null;

                $item->expiresAfter(self::CACHE_TTL);

                return $name;
            } catch (\Throwable) {
                // En caso de error, cachear poco tiempo para no bloquear
                $item->expiresAfter(self::CACHE_TTL_ERR);

                return null;
            }
        });
    }
}
