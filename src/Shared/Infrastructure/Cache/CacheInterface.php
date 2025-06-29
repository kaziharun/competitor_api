<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    public function delete(string $key): bool;

    public function exists(string $key): bool;

    public function clear(): bool;
}
