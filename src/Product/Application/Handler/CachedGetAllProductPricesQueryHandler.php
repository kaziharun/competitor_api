<?php

declare(strict_types=1);

namespace App\Product\Application\Handler;

use App\Product\Application\Query\GetAllProductPricesQuery;
use App\Product\Application\UseCase\CachedGetAllProductPricesUseCase;

final class CachedGetAllProductPricesQueryHandler
{
    public function __construct(
        private readonly CachedGetAllProductPricesUseCase $useCase,
    ) {
    }

    public function handle(GetAllProductPricesQuery $query): array
    {
        return $this->useCase->execute();
    }
}
