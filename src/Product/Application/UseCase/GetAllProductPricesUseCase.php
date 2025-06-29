<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase;

use App\Product\Domain\Repository\ProductPriceRepositoryInterface;

final class GetAllProductPricesUseCase
{
    public function __construct(
        private readonly ProductPriceRepositoryInterface $repository,
    ) {
    }

    public function execute(): array
    {
        try {
            $prices = $this->repository->findAll();

            return $prices;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
