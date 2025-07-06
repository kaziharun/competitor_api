<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\Service\ProductPriceCacheServiceInterface;

class GetAllProductPricesUseCase
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
        private readonly ProductPriceCacheServiceInterface $cacheService,
    ) {
    }

    public function execute(): array
    {
        $cachedData = $this->cacheService->getCachedProductList();

        if (null !== $cachedData) {
            return $cachedData;
        }

        $prices = $this->repository->findAll();

        if (!empty($prices)) {
            $this->cacheService->cacheProductList(productPrices: $prices);
        }

        return $prices;
    }
}
