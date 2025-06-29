<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\ExternalApi;

use App\Product\Domain\ValueObject\PriceData;
use App\Product\Domain\ValueObject\ProductId;

interface ExternalApiClientInterface
{
    /**
     * @return PriceData[]
     */
    public function fetchPricesForProduct(ProductId $productId): array;
}
