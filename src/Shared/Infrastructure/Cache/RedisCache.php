<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

use Predis\ClientInterface;

final class RedisCache implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function __construct(
        private readonly ClientInterface $redis,
    ) {
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);

        return $this->decode($value);
    }

    public function set(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool
    {
        $encoded = $this->encode($value);
        if (null === $encoded) {
            return false;
        }

        return 'OK' === $this->redis->setex($key, $ttl, $encoded);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del([$key]) > 0;
    }

    public function exists(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    public function clear(): bool
    {
        return 'OK' === $this->redis->flushdb();
    }

    public function has(string $key): bool
    {
        return $this->exists($key);
    }

    public function getMultiple(array $keys): array
    {
        $rawValues = $this->redis->mget($keys);
        $result = [];

        foreach ($keys as $index => $key) {
            $result[$key] = $this->decode($rawValues[$index] ?? null);
        }

        return $result;
    }

    public function setMultiple(array $values, int $ttl = self::DEFAULT_TTL): bool
    {
        $pipeline = $this->redis->pipeline();

        foreach ($values as $key => $value) {
            $encoded = $this->encode($value);
            if (null !== $encoded) {
                $pipeline->setex($key, $ttl, $encoded);
            }
        }

        $results = $pipeline->execute();

        return !in_array(false, $results, true);
    }

    public function deleteMultiple(array $keys): bool
    {
        return $this->redis->del($keys) > 0;
    }

    private function encode(mixed $value): ?string
    {
        $encoded = json_encode($value);

        return false === $encoded ? null : $encoded;
    }

    private function decode(?string $value): mixed
    {
        if (null === $value) {
            return null;
        }

        $decoded = json_decode($value, true);

        return JSON_ERROR_NONE === json_last_error() ? $decoded : null;
    }
}
