<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Domain\ValueObject\ProductId;

final class FetchPricesCommandData
{
    public function __construct(
        private readonly ProductId $productId,
    ) {
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
