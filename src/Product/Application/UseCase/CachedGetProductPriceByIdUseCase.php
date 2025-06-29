<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Infrastructure\Cache\ProductPriceCacheService;

/**
 * Cached version of GetProductPriceByIdUseCase that uses Redis caching.
 */
final class CachedGetProductPriceByIdUseCase
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
        private readonly ProductPriceCacheService $cacheService,
    ) {
    }

    /**
     * Execute the use case with caching.
     */
    public function execute(ProductId $productId): ?ProductPrice
    {
        $cachedData = $this->cacheService->getCachedProductPrice($productId);

        if (null !== $cachedData) {
            return $cachedData;
        }

        $productPrice = $this->repository->findByProductId($productId);

        if (null !== $productPrice) {
            $this->cacheService->cacheProductPrice($productPrice);
        }

        return $productPrice;
    }
}
