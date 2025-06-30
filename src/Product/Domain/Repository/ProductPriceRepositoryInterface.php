<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\ProductId;

interface ProductPriceRepositoryInterface
{
    public function save(\App\Shared\Domain\Entity\BaseEntity $entity): void;

    public function saveOrUpdate(ProductPrice $productPrice): void;

    public function saveAll(array $productPrices): void;

    public function findByProductId(ProductId $productId): ?ProductPrice;

    public function findAll(?int $limit = null): array;

    public function deleteByProductId(ProductId $productId): void;
}
