<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Cache;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\Service\ProductPriceCacheServiceInterface;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\VendorName;
use App\Shared\Infrastructure\Cache\RedisCache;

final class ProductPriceCacheService implements ProductPriceCacheServiceInterface
{
    private const CACHE_PREFIX = 'product_price:';
    private const CACHE_TTL = 3600;
    private const PRODUCT_LIST_KEY = 'product_list';

    public function __construct(
        private readonly RedisCache $cache,
        private readonly ProductPriceRepositoryInterface $repository,
    ) {
    }

    public function getCachedProductPrice(ProductId $productId): ?ProductPrice
    {
        $cacheKey = $this->buildCacheKey($productId->getValue());
        $cachedData = $this->cache->get($cacheKey);

        if (null === $cachedData) {
            return null;
        }

        try {
            return $this->deserializeProductPrice($cachedData);
        } catch (\Exception $e) {
            $this->cache->delete($cacheKey);

            return null;
        }
    }

    public function cacheProductPrice(ProductPrice $productPrice): void
    {
        $cacheKey = $this->buildCacheKey($productPrice->getProductId()->getValue());
        $serializedData = $this->serializeProductPrice($productPrice);

        $this->cache->set($cacheKey, $serializedData, self::CACHE_TTL);
    }

    public function invalidateProductPrice(ProductId $productId): void
    {
        $cacheKey = $this->buildCacheKey($productId->getValue());
        $this->cache->delete($cacheKey);
    }

    public function getOrFetchProductPrice(ProductId $productId): ?ProductPrice
    {
        $cachedPrice = $this->getCachedProductPrice($productId);

        if (null !== $cachedPrice) {
            return $cachedPrice;
        }

        $productPrice = $this->repository->findByProductId($productId);

        if (null !== $productPrice) {
            $this->cacheProductPrice($productPrice);
        }

        return $productPrice;
    }

    public function getCachedProductList(): ?array
    {
        $cachedData = $this->cache->get(self::PRODUCT_LIST_KEY);

        if (null === $cachedData) {
            return null;
        }

        try {
            return array_map(
                fn ($data) => $this->deserializeProductPrice($data),
                $cachedData
            );
        } catch (\Exception $e) {
            $this->cache->delete(self::PRODUCT_LIST_KEY);

            return null;
        }
    }

    public function cacheProductList(array $productPrices): void
    {
        $serializedData = array_map(
            fn ($productPrice) => $this->serializeProductPrice($productPrice),
            $productPrices
        );

        $this->cache->set(self::PRODUCT_LIST_KEY, $serializedData, self::CACHE_TTL);
    }

    private function buildCacheKey(string $productId): string
    {
        return self::CACHE_PREFIX.$productId;
    }

    private function serializeProductPrice(ProductPrice $productPrice): array
    {
        return [
            'product_id' => $productPrice->getProductId()->getValue(),
            'vendor_name' => $productPrice->getVendorName()->getValue(),
            'price' => $productPrice->getPrice()->getValue(),
            'fetched_at' => $productPrice->getFetchedAt()->getValue()->format('Y-m-d H:i:s'),
            'created_at' => $productPrice->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    private function deserializeProductPrice(array $data): ProductPrice
    {
        return new ProductPrice(
            new ProductId($data['product_id']),
            new VendorName($data['vendor_name']),
            new Price($data['price']),
            new FetchedAt(new \DateTimeImmutable($data['fetched_at']))
        );
    }
}
