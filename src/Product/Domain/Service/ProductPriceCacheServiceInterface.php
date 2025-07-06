<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;

interface ProductPriceCacheServiceInterface
{
    public function getCachedProductPrice(ProductId $productId): ?ProductPrice;

    public function cacheProductPrice(ProductPrice $productPrice): void;

    public function invalidateProductPrice(ProductId $productId): void;

    public function getOrFetchProductPrice(ProductId $productId): ?ProductPrice;

    public function getCachedProductList(): ?array;

    public function cacheProductList(array $productPrices): void;
}
