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

        if (null === $value) {
            return null;
        }

        return json_decode($value, true);
    }

    public function set(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool
    {
        $serializedValue = json_encode($value);

        if (false === $serializedValue) {
            return false;
        }

        $result = $this->redis->setex($key, $ttl, $serializedValue);

        return 'OK' === $result;
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }

    public function exists(string $key): bool
    {
        return $this->redis->exists($key);
    }

    public function clear(): bool
    {
        return 'OK' === $this->redis->flushdb();
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }

    public function getMultiple(array $keys): array
    {
        $values = $this->redis->mget($keys);
        $result = [];

        foreach ($keys as $index => $key) {
            $value = $values[$index] ?? null;
            $result[$key] = null === $value ? null : json_decode($value, true);
        }

        return $result;
    }

    public function setMultiple(array $values, int $ttl = self::DEFAULT_TTL): bool
    {
        $pipeline = $this->redis->pipeline();

        foreach ($values as $key => $value) {
            $serializedValue = json_encode($value);
            if (false !== $serializedValue) {
                $pipeline->setex($key, $ttl, $serializedValue);
            }
        }

        $results = $pipeline->execute();

        return !in_array(false, $results, true);
    }

    public function deleteMultiple(array $keys): bool
    {
        return $this->redis->del($keys) > 0;
    }
}
