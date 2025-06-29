<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

final class RedisCache implements CacheInterface
{
    private const DEFAULT_TTL = 3600;

    public function __construct(
        private readonly \Redis $redis,
    ) {
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);

        if (false === $value) {
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

        return $this->redis->setex($key, $ttl, $serializedValue);
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
        return $this->redis->flushDB();
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
            $value = $values[$index] ?? false;
            $result[$key] = false === $value ? null : json_decode($value, true);
        }

        return $result;
    }

    public function setMultiple(array $values, int $ttl = self::DEFAULT_TTL): bool
    {
        $pipeline = $this->redis->multi();

        foreach ($values as $key => $value) {
            $serializedValue = json_encode($value);
            if (false !== $serializedValue) {
                $pipeline->setex($key, $ttl, $serializedValue);
            }
        }

        $results = $pipeline->exec();

        return !in_array(false, $results, true);
    }

    public function deleteMultiple(array $keys): bool
    {
        return $this->redis->del($keys) > 0;
    }
}
