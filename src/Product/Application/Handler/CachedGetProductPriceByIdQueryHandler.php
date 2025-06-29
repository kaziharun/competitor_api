<?php

declare(strict_types=1);

namespace App\Product\Application\Handler;

use App\Product\Application\Query\GetProductPriceByIdQuery;
use App\Product\Application\UseCase\CachedGetProductPriceByIdUseCase;
use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;

final class CachedGetProductPriceByIdQueryHandler
{
    public function __construct(
        private readonly CachedGetProductPriceByIdUseCase $useCase,
    ) {
    }

    public function handle(GetProductPriceByIdQuery $query): ?ProductPrice
    {
        $productId = new ProductId($query->getProductId());

        return $this->useCase->execute($productId);
    }
}
