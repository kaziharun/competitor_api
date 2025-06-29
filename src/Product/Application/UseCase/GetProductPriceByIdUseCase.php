<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\ValueObject\ProductId;

final class GetProductPriceByIdUseCase
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
    ) {
    }

    public function execute(ProductId $productId): ?ProductPrice
    {
        try {
            return $this->repository->findByProductId($productId);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
