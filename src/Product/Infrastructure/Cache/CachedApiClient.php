<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Cache;

use App\Product\Domain\ValueObject\ProductId;
use App\Product\Infrastructure\Api\ExternalApiInterface;
use App\Shared\Infrastructure\Cache\CacheInterface;

final class CachedApiClient implements ExternalApiInterface
{
    private const CACHE_TTL = 300;
    private const CACHE_KEY_PREFIX = 'api_response:';

    public function __construct(
        private readonly ExternalApiInterface $apiClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getCompetitorPrices(ProductId $productId): ?array
    {
        $cacheKey = self::CACHE_KEY_PREFIX.$productId->getValue();
        $cachedData = $this->cache->get($cacheKey);

        if (null !== $cachedData) {
            return $cachedData;
        }

        $apiData = $this->apiClient->getCompetitorPrices($productId);

        if (null !== $apiData) {
            $this->cache->set($cacheKey, $apiData, self::CACHE_TTL);
        }

        return $apiData;
    }

    public function getProviderName(): string
    {
        return $this->apiClient->getProviderName();
    }

    public function isAvailable(): bool
    {
        return $this->apiClient->isAvailable();
    }

    public function getRateLimitInfo(): array
    {
        return $this->apiClient->getRateLimitInfo();
    }
}
