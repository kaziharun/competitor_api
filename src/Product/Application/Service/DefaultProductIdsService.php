<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

final class DefaultProductIdsService
{
    private const DEFAULT_PRODUCT_IDS = ['123', '456', '789'];

    public function getDefaultProductIds(): array
    {
        return self::DEFAULT_PRODUCT_IDS;
    }

    public function hasDefaultProductId(string $productId): bool
    {
        return in_array($productId, self::DEFAULT_PRODUCT_IDS, true);
    }

    public function getDefaultProductIdsCount(): int
    {
        return count(self::DEFAULT_PRODUCT_IDS);
    }
}
