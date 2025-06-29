<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Infrastructure\Cache\ProductPriceCacheService;

/**
 * Cached version of GetAllProductPricesUseCase that uses Redis caching.
 */
final class CachedGetAllProductPricesUseCase
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
        private readonly ProductPriceCacheService $cacheService,
    ) {
    }

    /**
     * Execute the use case with caching.
     *
     * @return ProductPrice[]
     */
    public function execute(): array
    {
        $cachedData = $this->cacheService->getCachedProductList();

        if (null !== $cachedData) {
            return $cachedData;
        }

        $prices = $this->repository->findAll();

        if (!empty($prices)) {
            $this->cacheService->cacheProductList($prices);
        }

        return $prices;
    }
}
