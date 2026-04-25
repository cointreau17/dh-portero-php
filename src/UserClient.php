<?php

declare(strict_types=1);

namespace Dh\Portero;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserClient
{
    private const CACHE_PREFIX = 'dh_user_name_';
    private const CACHE_TTL    = 3600; // 1 hora
    private const CACHE_TTL_ERR = 60; // reintento rápido si falla

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly string $baseUrl,
    ) {}

    /**
     * Devuelve el nombre del usuario dado su UUID.
     * Devuelve null si no existe o si el servicio no está disponible.
     */
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
