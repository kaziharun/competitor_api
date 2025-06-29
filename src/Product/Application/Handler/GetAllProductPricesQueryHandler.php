<?php

declare(strict_types=1);

namespace App\Product\Application\Handler;

use App\Product\Application\DTO\Response\ProductPriceResponse;
use App\Product\Application\Query\GetAllProductPricesQuery;

final class GetAllProductPricesQueryHandler
{
    /**
     * @return ProductPriceResponse[]
     */
    public function handle(GetAllProductPricesQuery $query): array
    {
        return $query->execute();
    }
}
