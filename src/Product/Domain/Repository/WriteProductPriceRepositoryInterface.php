<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;

interface WriteProductPriceRepositoryInterface
{
    public function save(ProductPrice $productPrice): void;

    public function findByProductId(ProductId $productId): ?ProductPrice;

    /**
     * @param array<ProductPrice> $productPrices
     */
    public function saveAll(array $productPrices): void;

    public function deleteByProductId(ProductId $productId): void;
}
