<?php

declare(strict_types=1);

namespace App\Product\Application\Handler;

use App\Product\Application\DTO\Response\ProductPriceResponse;
use App\Product\Application\Query\GetProductPriceByIdQuery;
use App\Product\Domain\ValueObject\ProductId;

final class GetProductPriceByIdQueryHandler
{
    public function handle(GetProductPriceByIdQuery $query, ProductId $productId): ?ProductPriceResponse
    {
        return $query->execute($productId);
    }
}
