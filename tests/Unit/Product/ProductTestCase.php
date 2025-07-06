<?php

declare(strict_types=1);

namespace Tests\Unit\Product;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\VendorName;
use PHPUnit\Framework\TestCase;

abstract class ProductTestCase extends TestCase
{
    protected function createSampleProductPrice(string $productId = '123'): ProductPrice
    {
        return new ProductPrice(
            new ProductId($productId),
            new VendorName('Vendor A'),
            new Price(10.50),
            new FetchedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
        );
    }
}
