<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Cache;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;
use App\Shared\Infrastructure\Cache\CacheInterface;

/**
 * Cache service for product prices with different caching strategies.
 */
final class ProductPriceCacheService
{
    private const CACHE_TTL = 300;
    private const PRODUCT_PRICE_KEY_PREFIX = 'product_price:';
    private const PRODUCT_LIST_KEY = 'product_list';

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Cache API response for a specific product.
     */
    public function cacheApiResponse(ProductId $productId, array $priceData, int $ttl = self::CACHE_TTL): bool
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();

        return $this->cache->set($cacheKey, $priceData, $ttl);
    }

    /**
     * Get cached API response for a specific product.
     */
    public function getCachedApiResponse(ProductId $productId): ?array
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();

        $cachedData = $this->cache->get($cacheKey);

        return $cachedData;
    }

    /**
     * Cache individual product price entity.
     */
    public function cacheProductPrice(ProductPrice $productPrice): bool
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productPrice->getProductId()->getValue();
        $serializedData = $this->serializeProductPrice($productPrice);

        return $this->cache->set($cacheKey, $serializedData, self::CACHE_TTL);
    }

    /**
     * Get cached product price.
     */
    public function getCachedProductPrice(ProductId $productId): ?ProductPrice
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();
        $cachedData = $this->cache->get($cacheKey);

        if (null === $cachedData) {
            return null;
        }

        return $this->deserializeProductPrice($cachedData);
    }

    /**
     * Cache product list (all products).
     */
    public function cacheProductList(array $productPrices): bool
    {
        $serializedData = array_map(
            fn ($productPrice) => $this->serializeProductPrice($productPrice),
            $productPrices
        );

        return $this->cache->set(self::PRODUCT_LIST_KEY, $serializedData, self::CACHE_TTL);
    }

    /**
     * Get cached product list.
     */
    public function getCachedProductList(): ?array
    {
        $cachedData = $this->cache->get(self::PRODUCT_LIST_KEY);

        if (null === $cachedData) {
            return null;
        }

        return array_map(
            fn ($data) => $this->deserializeProductPrice($data),
            $cachedData
        );
    }

    /**
     * Cache aggregated prices for a product.
     */
    public function cacheAggregatedPrices(ProductId $productId, array $aggregatedData, int $ttl = self::CACHE_TTL): bool
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();

        return $this->cache->set($cacheKey, $aggregatedData, $ttl);
    }

    /**
     * Get cached aggregated prices.
     */
    public function getCachedAggregatedPrices(ProductId $productId): ?array
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();

        $cachedData = $this->cache->get($cacheKey);

        return $cachedData;
    }

    /**
     * Invalidate cache for a specific product.
     */
    public function invalidateProductCache(ProductId $productId): bool
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();

        return $this->cache->delete($cacheKey);
    }

    /**
     * Invalidate all product-related cache.
     */
    public function invalidateAllProductCache(): bool
    {
        return $this->cache->delete(self::PRODUCT_LIST_KEY);
    }

    /**
     * Get cache statistics.
     */
    public function getCacheStats(): array
    {
        // This would require additional Redis commands to get actual statistics
        // For now, return basic info
        return [
            'cache_service' => 'ProductPriceCacheService',
            'ttl' => self::CACHE_TTL,
        ];
    }

    private function serializeProductPrice(ProductPrice $productPrice): array
    {
        return [
            'product_id' => $productPrice->getProductId()->getValue(),
            'vendor_name' => $productPrice->getVendorName()->getValue(),
            'price' => $productPrice->getPrice()->getValue(),
            'fetched_at' => $productPrice->getFetchedAt()->getValue()->format('Y-m-d H:i:s'),
            'created_at' => $productPrice->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $productPrice->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    private function deserializeProductPrice(array $data): ProductPrice
    {
        return new ProductPrice(
            new ProductId($data['product_id']),
            new \App\Product\Domain\ValueObject\VendorName($data['vendor_name']),
            new \App\Product\Domain\ValueObject\Price($data['price']),
            new \App\Product\Domain\ValueObject\FetchedAt(new \DateTimeImmutable($data['fetched_at']))
        );
    }
}
