<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Cache;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;
use App\Shared\Infrastructure\Cache\CacheInterface;

final class ProductPriceCacheService
{
    private const CACHE_TTL = 300;
    private const PRODUCT_PRICE_KEY_PREFIX = 'product_price:';
    private const PRODUCT_LIST_KEY = 'product_list';

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    public function cacheProductPrice(ProductPrice $productPrice): bool
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productPrice->getProductId()->getValue();
        $serializedData = $this->serializeProductPrice($productPrice);

        return $this->cache->set($cacheKey, $serializedData, self::CACHE_TTL);
    }

    public function getCachedProductPrice(ProductId $productId): ?ProductPrice
    {
        $cacheKey = self::PRODUCT_PRICE_KEY_PREFIX.$productId->getValue();
        $cachedData = $this->cache->get($cacheKey);

        if (null === $cachedData) {
            return null;
        }

        return $this->deserializeProductPrice($cachedData);
    }

    public function cacheProductList(array $productPrices): bool
    {
        $serializedData = array_map(
            fn ($productPrice) => $this->serializeProductPrice($productPrice),
            $productPrices
        );

        return $this->cache->set(self::PRODUCT_LIST_KEY, $serializedData, self::CACHE_TTL);
    }

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