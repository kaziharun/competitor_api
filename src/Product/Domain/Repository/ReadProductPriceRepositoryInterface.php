<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;

interface ReadProductPriceRepositoryInterface
{
    public function findByProductId(ProductId $productId): ?ProductPrice;

    /**
     * @return array<ProductPrice>
     */
    public function findAll(): array;
}
