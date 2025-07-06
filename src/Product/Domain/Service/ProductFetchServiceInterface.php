<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use App\Product\Domain\ValueObject\ProductId;

interface ProductFetchServiceInterface
{
    public function fetchPricesForProduct(ProductId $productId): void;

    public function fetchPricesForAllDefaultProducts(): void;
}
